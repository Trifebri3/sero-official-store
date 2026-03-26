<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $category_filter = '';

    public function delete($id)
    {
        Product::destroy($id);
        session()->flash('message', 'Masterpiece has been removed from the collection.');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCategoryFilter() { $this->resetPage(); }

    public function with(): array
    {
        $query = Product::with('category')
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            });

        if ($this->category_filter) {
            $query->where('category_id', $this->category_filter);
        }

        return [
            // Kita gunakan paginate agar load tetap ringan, tapi desain grid tetap penuh
            'products' => $query->latest()->paginate(12),
            'categories' => Category::all(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-12">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-8 border-b border-gray-100 pb-10">
        <div class="space-y-2">
            <span class="text-[#C5A358] text-[10px] font-black uppercase tracking-[0.5em] block">Inventory Vault</span>
            <h2 class="font-serif text-5xl text-[#1C1C1C] italic leading-tight">Curated Collection</h2>
        </div>

        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            <div class="relative flex-grow md:w-72">
                <input type="text" wire:model.live="search" placeholder="Search masterpiece..."
                       class="w-full border-none bg-[#F5F2ED] rounded-2xl px-6 py-4 text-xs font-serif italic focus:ring-1 focus:ring-[#C5A358] shadow-sm transition-all">
            </div>

            <select wire:model.live="category_filter"
                    class="border-none bg-[#F5F2ED] rounded-2xl py-4 px-6 text-[10px] font-black uppercase tracking-widest text-gray-500 focus:ring-1 focus:ring-[#C5A358] shadow-sm cursor-pointer">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <a href="{{ route('admin.products.create') }}"
               class="bg-[#1C1C1C] text-[#C5A358] px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl flex items-center gap-2">
                <span>+ Add Item</span>
            </a>
        </div>
    </header>

    @if($products->isEmpty())
        <div class="py-40 text-center bg-[#FDFBF9] rounded-[3rem] border border-dashed border-gray-200">
            <div class="font-serif text-2xl text-gray-300 italic uppercase tracking-widest">No pieces found in the vault.</div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
            @foreach($products as $product)
                <div class="group relative flex flex-col h-full bg-white rounded-[2.5rem] overflow-hidden transition-all duration-700 hover:shadow-2xl border border-gray-50">

                    <div class="absolute top-6 right-6 z-20 flex flex-col gap-3 opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0 transition-all duration-500">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="p-3 bg-white shadow-xl rounded-full text-gray-400 hover:text-[#C5A358] transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2"/></svg>
                        </a>
                        <button wire:click="delete({{ $product->id }})" wire:confirm="Remove this piece?" class="p-3 bg-white shadow-xl rounded-full text-gray-400 hover:text-red-400 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                        </button>
                    </div>

                    <div class="relative aspect-[4/5] overflow-hidden bg-[#F5F2ED]">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/600x800/FDFBF9/C5A358?text=SERO' }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[2s] grayscale-[20%] group-hover:grayscale-0">

                        <div class="absolute bottom-6 left-6">
                            @if($product->stock <= 0)
                                <span class="px-4 py-2 bg-black/80 backdrop-blur text-white text-[8px] font-black uppercase tracking-[0.2em] rounded-full">Out of Stock</span>
                            @elseif($product->stock < 5)
                                <span class="px-4 py-2 bg-[#C5A358] text-white text-[8px] font-black uppercase tracking-[0.2em] rounded-full animate-pulse">Low Reserve</span>
                            @else
                                <span class="px-4 py-2 bg-white/90 backdrop-blur text-[#1C1C1C] text-[8px] font-black uppercase tracking-[0.2em] rounded-full shadow-sm">Available</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-8 flex flex-col flex-grow space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="text-[9px] font-black text-[#C5A358] uppercase tracking-[0.3em]">{{ $product->category->name ?? 'Aesthetic Piece' }}</span>
                            <span class="text-[9px] font-mono text-gray-300">#{{ $product->sku }}</span>
                        </div>

                        <h3 class="font-serif text-xl text-[#1C1C1C] italic leading-tight group-hover:text-[#C5A358] transition-colors">
                            {{ $product->name }}
                        </h3>

                        <div class="pt-4 border-t border-gray-50 mt-auto flex justify-between items-end">
                            <div>
                                <p class="text-[8px] text-gray-400 uppercase font-black tracking-widest mb-1">Valuation</p>
                                <p class="font-serif text-xl text-[#1C1C1C]">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] text-gray-400 uppercase font-black tracking-widest mb-1">Stock</p>
                                <p class="text-xs font-bold text-[#1C1C1C]">{{ $product->stock }} <span class="font-light italic text-gray-300 ml-1">units</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-16">
            {{ $products->links() }}
        </div>
    @endif
</div>
