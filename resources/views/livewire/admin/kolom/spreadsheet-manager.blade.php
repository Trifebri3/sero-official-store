<?php

use Livewire\Volt\Component;
use App\Models\Category;
use App\Models\CategoryTemplate;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $categories;
    public $selectedCategoryId = '';
    public $columns = []; // Tempat menyimpan struktur kolom (nama & tipe)

    public function mount()
    {
        $this->categories = Category::all();
    }

    // Load template saat kategori dipilih
    public function updatedSelectedCategoryId($id)
    {
        if (!$id) {
            $this->columns = [];
            return;
        }

        $template = CategoryTemplate::where('category_id', $id)->first();
        $this->columns = $template ? $template->schema : [];
    }

    // Tambah baris baru (kolom baru di spreadsheet)
    public function addColumn()
    {
        $this->columns[] = ['name' => '', 'type' => 'text'];
    }

    // Hapus kolom
    public function removeColumn($index)
    {
        unset($this->columns[$index]);
        $this->columns = array_values($this->columns);
    }

    // Simpan Struktur ke Database
    public function saveTemplate()
    {
        $this->validate([
            'selectedCategoryId' => 'required',
            'columns.*.name' => 'required|string|min:1',
        ], [
            'columns.*.name.required' => 'Nama kolom tidak boleh kosong.',
        ]);

        CategoryTemplate::updateOrCreate(
            ['category_id' => $this->selectedCategoryId],
            ['schema' => $this->columns]
        );

        session()->flash('success', 'Masterpiece Template has been synchronized.');
    }
}; ?>

<div class="p-6 lg:p-10 bg-[#FDFBF9] min-h-screen">
    <div class="max-w-6xl mx-auto">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <h2 class="font-serif italic text-4xl text-[#1C1C1C] mb-2">Dynamic Vault Architect</h2>
                <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[#C5A358]">Define your collection structure</p>
            </div>

            <div class="flex gap-4">
                <button wire:click="addColumn" class="px-6 py-3 bg-white border border-black text-[10px] font-black uppercase tracking-widest hover:bg-black hover:text-white transition-all">
                    + Add Attribute
                </button>
                <button wire:click="saveTemplate" class="px-8 py-3 bg-[#C5A358] text-white text-[10px] font-black uppercase tracking-widest shadow-xl hover:bg-[#b08e46] transition-all">
                    Save Blueprint
                </button>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-8 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-xs font-serif italic">
                {{ session('success') }}
            </div>
        @endif

        {{-- Selection Section --}}
        <div class="bg-white p-8 rounded-[2rem] shadow-sm mb-10 border border-black/5">
            <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-4">Target Category</label>
            <select wire:model.live="selectedCategoryId" class="w-full md:w-1/3 bg-[#FDFBF9] border-none rounded-xl px-6 py-4 text-sm font-serif italic focus:ring-1 focus:ring-[#C5A358]">
                <option value="">Select a Collection...</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Spreadsheet Style UI --}}
        <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-black/5">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#1C1C1C] text-white">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.3em] w-16 text-center">No</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.3em]">Attribute Name</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.3em]">Data Type</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($columns as $index => $col)
                        <tr class="group hover:bg-[#FDFBF9] transition-colors">
                            <td class="px-8 py-6 text-xs font-mono text-gray-300 text-center">{{ $index + 1 }}</td>
                            <td class="px-8 py-6">
                                <input type="text" wire:model="columns.{{ $index }}.name"
                                    placeholder="e.g. Material, Wattage, Dimension..."
                                    class="w-full bg-transparent border-none p-0 text-sm font-serif italic focus:ring-0 placeholder:text-gray-200">
                            </td>
                            <td class="px-8 py-6">
                                <select wire:model="columns.{{ $index }}.type" class="bg-transparent border-none p-0 text-[10px] font-black uppercase tracking-widest focus:ring-0 text-[#C5A358]">
                                    <option value="text">Text (String)</option>
                                    <option value="number">Numeric (IDR/Unit)</option>
                                    <option value="longtext">Description (Long)</option>
                                    <option value="date">Timeline (Date)</option>
                                </select>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button wire:click="removeColumn({{ $index }})" class="opacity-0 group-hover:opacity-100 p-2 text-red-300 hover:text-red-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-20 text-center">
                                <p class="font-serif italic text-gray-300 text-xl">Select a category to start architecting.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Brand --}}
        <div class="mt-12 flex items-center justify-center gap-4 opacity-20">
            <div class="h-[1px] w-12 bg-black"></div>
            <span class="text-[8px] font-black uppercase tracking-[0.5em]">SERO ARCHITECT SYSTEM</span>
            <div class="h-[1px] w-12 bg-black"></div>
        </div>
    </div>
</div>
