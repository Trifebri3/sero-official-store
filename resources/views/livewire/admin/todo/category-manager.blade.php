<?php

use Livewire\Volt\Component;
use App\Models\TodoCategory;
use Illuminate\Support\Str;

new class extends Component {
    public $categories;
    public $name = '';
    public $columns = []; // Array untuk menyimpan nama-nama kolom dinamis

    public function mount() {
        $this->categories = TodoCategory::all();
    }

    public function addColumn() {
        $this->columns[] = ['name' => ''];
    }

    public function removeColumn($index) {
        unset($this->columns[$index]);
        $this->columns = array_values($this->columns);
    }

    public function saveCategory() {
        $this->validate([
            'name' => 'required|min:3',
            'columns.*.name' => 'required'
        ]);

        TodoCategory::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'schema' => $this->columns
        ]);

        $this->reset(['name', 'columns']);
        $this->categories = TodoCategory::all();
        session()->flash('success', 'Arsitektur Kategori Baru Berhasil Terarsip.');
    }

    public function deleteCategory($id) {
        TodoCategory::find($id)->delete();
        $this->categories = TodoCategory::all();
    }
}; ?>

<div class="p-6 md:p-12">
    <div class="max-w-4xl mx-auto">
        <div class="mb-12">
            <h2 class="font-serif italic text-4xl text-[#1C1C1C] mb-2">Category Architect</h2>
            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[#C5A358]">Define Operational Structures</p>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl shadow-black/5 border border-black/5 mb-12">
            <div class="space-y-8">
                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-400 block mb-4">Category Name</label>
                    <input type="text" wire:model="name" placeholder="e.g. Marketing, Development, Maintenance..."
                           class="w-full bg-[#FDFBF9] border-none rounded-2xl px-6 py-4 text-sm font-serif italic focus:ring-2 focus:ring-[#C5A358]/20 transition-all">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-6">
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-400">Dynamic Columns (Blueprint)</label>
                        <button wire:click="addColumn" class="text-[9px] font-black uppercase tracking-widest text-[#C5A358] hover:underline">+ Add Attribute</button>
                    </div>

                    <div class="space-y-3">
                        @foreach($columns as $index => $col)
                        <div class="flex gap-4 items-center animate-in slide-in-from-left duration-300">
                            <input type="text" wire:model="columns.{{ $index }}.name" placeholder="Column Name (e.g. Deadline)"
                                   class="flex-grow bg-[#FDFBF9] border-none rounded-xl px-6 py-3 text-xs font-serif italic focus:ring-1 focus:ring-[#C5A358]">
                            <button wire:click="removeColumn({{ $index }})" class="text-red-300 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button wire:click="saveCategory" class="w-full bg-[#1C1C1C] text-white text-[10px] font-black uppercase tracking-[0.3em] py-5 rounded-2xl hover:bg-[#C5A358] transition-all shadow-xl">
                    Finalize Structure
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($categories as $cat)
            <div class="bg-white p-6 rounded-[2rem] border border-black/5 flex justify-between items-center group hover:shadow-lg transition-all">
                <div>
                    <h4 class="font-serif italic text-lg">{{ $cat->name }}</h4>
                    <p class="text-[8px] font-black uppercase tracking-widest text-gray-300 mt-1">
                        {{ count($cat->schema) }} Attributes defined
                    </p>
                </div>
                <button wire:click="deleteCategory({{ $cat->id }})" class="opacity-0 group-hover:opacity-100 p-3 text-gray-200 hover:text-red-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>
