<?php

use Livewire\Volt\Component;
use App\Models\Category;

new class extends Component {
    public string $name = '';
    public array $default_specs = ['Material', 'Dimensi']; // Default awal
    public array $default_marketing = ['Canva Link'];

    // Menambah baris atribut baru ke template
    public function addSpecField() { $this->default_specs[] = ''; }
    public function removeSpecField($index) { unset($this->default_specs[$index]); $this->default_specs = array_values($this->default_specs); }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|unique:categories,name',
            'default_specs' => 'required|array|min:1',
        ]);

        Category::create([
            'name' => $this->name,
            'default_specs' => array_filter($this->default_specs), // Buang yang kosong
            'default_marketing' => array_filter($this->default_marketing),
        ]);

        $this->reset(['name', 'default_specs', 'default_marketing']);
        session()->flash('message', 'Template Kategori Berhasil Dibuat!');
    }
}; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-luxury-cream min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-luxury border border-gray-100 h-fit">
        <h2 class="font-serif text-2xl text-luxury-dark mb-6 tracking-tight">Create New Table Template</h2>

        <form wire:submit="saveCategory" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-luxury-dark mb-2 uppercase tracking-widest">Category Name</label>
                <input type="text" wire:model="name" placeholder="ex: Sofa Collection, Minimalist Lamp"
                       class="w-full border-gray-200 rounded-xl focus:ring-luxury-gold shadow-sm">
            </div>

            <hr class="border-luxury-cream">

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <label class="text-sm font-bold text-luxury-gold uppercase tracking-widest">Required Specs Table</label>
                    <button type="button" wire:click="addSpecField" class="text-xs bg-luxury-dark text-white px-3 py-1 rounded-full">+ Add Column</button>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    @foreach($default_specs as $index => $field)
                    <div class="flex gap-2">
                        <input type="text" wire:model="default_specs.{{ $index }}" placeholder="Column Name (ex: Wood Type)"
                               class="w-full text-sm border-gray-100 bg-gray-50 rounded-lg">
                        <button type="button" wire:click="removeSpecField({{ $index }})" class="text-red-300">×</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full bg-luxury-orange text-white py-4 rounded-xl font-serif shadow-lg hover:opacity-90 transition">
                Save Template Structure
            </button>
        </form>
    </div>

    <div class="space-y-4">
        <h3 class="font-serif text-xl text-luxury-dark px-2">Existing Templates</h3>
        @foreach(\App\Models\Category::all() as $cat)
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 group hover:border-luxury-gold transition">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-bold text-luxury-dark">{{ $cat->name }}</h4>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($cat->default_specs as $spec)
                        <span class="text-[10px] bg-luxury-cream text-luxury-gold px-2 py-1 rounded-md border border-luxury-gold/10 font-medium">
                            {{ $spec }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition">
                    <button class="text-xs text-gray-400 hover:text-red-500">Delete</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
