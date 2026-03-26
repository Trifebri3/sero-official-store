<?php

use Livewire\Volt\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDynamicData;

new class extends Component {
    public $selectedCategoryId = '';
    public $template = null;
    public $products = [];
    public $values = []; // Array untuk menampung input sementara [product_id][column_name]

    // 1. Ambil Kategori & Produk saat kategori dipilih
    public function updatedSelectedCategoryId($id)
    {
        if (!$id) {
            $this->reset(['template', 'products', 'values']);
            return;
        }

        $category = Category::with(['template', 'products.dynamic'])->find($id);

        if (!$category || !$category->template) {
            $this->template = null;
            $this->products = [];
            return;
        }

        $this->template = $category->template;
        $this->products = $category->products;

        // 2. Map data yang sudah ada ke array values agar muncul di input
        foreach ($this->products as $product) {
            foreach ($this->template->schema as $col) {
                $colName = $col['name'];
                $this->values[$product->id][$colName] = $product->dynamic->values[$colName] ?? '';
            }
        }
    }

    // 3. Simpan Data (Bulk Save)
    public function saveAll()
    {
        foreach ($this->values as $productId => $dynamicValues) {
            ProductDynamicData::updateOrCreate(
                ['product_id' => $productId],
                ['values' => $dynamicValues]
            );
        }

        session()->flash('success', 'The Masterpiece Vault has been updated.');
    }
}; ?>

<div class="p-4 md:p-10">
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-10">
        <div class="w-full md:w-1/3">
            <label class="text-[9px] font-black uppercase tracking-widest text-[#C5A358] block mb-3">Filter Collection</label>
            <select wire:model.live="selectedCategoryId" class="w-full bg-white border border-black/5 rounded-2xl px-6 py-4 text-sm font-serif italic shadow-sm focus:ring-1 focus:ring-[#C5A358]">
                <option value="">Choose Category...</option>
                @foreach(App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        @if($template)
        <button wire:click="saveAll" class="w-full md:w-auto px-10 py-4 bg-[#1C1C1C] text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-full hover:bg-[#C5A358] transition-all shadow-xl">
            Synchronize Data
        </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="mb-8 p-4 bg-black text-white text-[10px] font-black uppercase tracking-widest animate-pulse">
            {{ session('success') }}
        </div>
    @endif

    @if($template && count($products) > 0)
    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-black/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#FDFBF9] border-b border-black/5">
                        <th class="px-8 py-6 text-[9px] font-black uppercase tracking-widest text-gray-400 min-w-[200px]">Product Name</th>
                        @foreach($template->schema as $col)
                            <th class="px-8 py-6 text-[9px] font-black uppercase tracking-widest text-black border-l border-black/5">
                                {{ $col['name'] }}
                                <span class="block text-[7px] text-[#C5A358] mt-1">({{ $col['type'] }})</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($products as $product)
                        <tr class="hover:bg-[#FDFBF9]/50 transition-colors">
                            <td class="px-8 py-5">
                                <p class="text-sm font-serif italic text-black">{{ $product->name }}</p>
                                <p class="text-[8px] font-mono text-gray-300 mt-1 uppercase">{{ $product->sku }}</p>
                            </td>

                            @foreach($template->schema as $col)
                                <td class="px-4 py-5 border-l border-black/5">
                                    <input type="{{ $col['type'] == 'number' ? 'number' : 'text' }}"
                                           wire:model="values.{{ $product->id }}.{{ $col['name'] }}"
                                           placeholder="..."
                                           class="w-full bg-transparent border-none p-4 text-xs font-serif focus:ring-1 focus:ring-[#C5A358]/20 rounded-lg placeholder:text-gray-200">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($selectedCategoryId)
    <div class="py-20 text-center bg-white rounded-[3rem] border border-dashed border-gray-200">
        <p class="font-serif italic text-gray-400">This collection has no architectural blueprint or products.</p>
    </div>
    @endif

    {{-- Luxury Footer Decoration --}}
    <div class="mt-10 flex justify-end">
        <span class="text-[7px] font-black uppercase tracking-[1em] text-gray-200">Sero Living Spreadsheet Engine v1.0</span>
    </div>
</div>
