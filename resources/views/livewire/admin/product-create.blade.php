<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public $price = 0;
    public $category_id = '';
    public int $stock = 0;

    // Properti untuk Foto (Array)
    public $photos = [];

    // Input Dinamis
    public array $dynamicSpecs = [];
    public array $dynamicMarketing = [];

    public function mount()
    {
        $this->dynamicSpecs = [['key' => '', 'value' => '']];
        $this->dynamicMarketing = [['key' => '', 'value' => '']];
    }

    public function updatedCategoryId($id)
    {
        $category = Category::find($id);
        if ($category) {
            $this->dynamicSpecs = collect($category->default_specs)
                ->map(fn($item) => ['key' => $item, 'value' => ''])
                ->toArray();

            $this->dynamicMarketing = collect($category->default_marketing)
                ->map(fn($item) => ['key' => $item, 'value' => ''])
                ->toArray();
        }
    }

    // Logic Hapus Foto Sebelum Save
    public function removePhoto($index)
    {
        array_splice($this->photos, $index, 1);
    }

    // Fitur Tambah/Hapus Baris
    public function addSpec() { $this->dynamicSpecs[] = ['key' => '', 'value' => '']; }
    public function removeSpec($index) {
        unset($this->dynamicSpecs[$index]);
        $this->dynamicSpecs = array_values($this->dynamicSpecs);
    }

    public function addMarketing() { $this->dynamicMarketing[] = ['key' => '', 'value' => '']; }
    public function removeMarketing($index) {
        unset($this->dynamicMarketing[$index]);
        $this->dynamicMarketing = array_values($this->dynamicMarketing);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'photos.*' => 'image|max:5120', // Batasi 5MB per foto
        ]);

        // 1. Handle Upload Multiple Foto
        $imagePaths = [];
        if ($this->photos) {
            foreach ($this->photos as $photo) {
                // Simpan ke storage/app/public/products
                $imagePaths[] = $photo->store('products', 'public');
            }
        }

        // 2. Mapping JSON dari Dynamic Inputs
        $finalSpecs = collect($this->dynamicSpecs)
            ->pluck('value', 'key')
            ->filter(fn($v, $k) => !empty($k))
            ->toArray();

        $finalMarketing = collect($this->dynamicMarketing)
            ->pluck('value', 'key')
            ->filter(fn($v, $k) => !empty($k))
            ->toArray();

        // 3. Simpan ke Database
        Product::create([
            'sku' => 'LX-' . strtoupper(Str::random(6)),
            'category_id' => $this->category_id,
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'price' => $this->price,
            'stock' => $this->stock,
            'specifications' => $finalSpecs,
            'marketing_assets' => $finalMarketing,
            'media' => $imagePaths, // Tersimpan sebagai Array JSON
            'is_published' => true,
        ]);

        session()->flash('message', 'Masterpiece created successfully!');
        $this->redirect(route('admin.products.index'));
    }
}; ?>

<div class="p-8 bg-[#FDFBF9] min-h-screen">
    <div class="max-w-5xl mx-auto">
        <form wire:submit="save" class="space-y-10 bg-white p-10 rounded-3xl shadow-luxury border border-gray-100">

            <div class="border-b border-luxury-cream pb-6">
                <h2 class="font-serif text-3xl text-luxury-dark italic">Create New Masterpiece</h2>
                <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">Sero Inventory Management System</p>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-[0.2em]">Visual Gallery</label>
                    <div wire:loading wire:target="photos" class="text-[10px] text-luxury-gold animate-pulse font-bold uppercase">Uploading...</div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    @foreach($photos as $index => $photo)
                        <div class="relative group aspect-square rounded-2xl overflow-hidden border-2 border-luxury-cream shadow-sm">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                            <button type="button" wire:click="removePhoto({{ $index }})"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-all shadow-xl hover:scale-110">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endforeach

                    <label class="flex flex-col items-center justify-center aspect-square border-2 border-dashed border-gray-200 rounded-2xl hover:border-luxury-gold hover:bg-luxury-cream/20 transition cursor-pointer group relative">
                        <svg class="w-8 h-8 text-gray-300 group-hover:text-luxury-gold transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/></svg>
                        <span class="text-[9px] font-bold text-gray-400 mt-2 uppercase tracking-widest group-hover:text-luxury-gold text-center px-2">Click to add multiple images</span>
                        <input type="file" wire:model="photos" multiple class="absolute inset-0 opacity-0 cursor-pointer">
                    </label>
                </div>
                @error('photos.*') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-4">
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Product Name</label>
                    <input type="text" wire:model="name" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3.5 focus:ring-luxury-gold focus:bg-white transition-all shadow-sm">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Category Template</label>
                    <select wire:model.live="category_id" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3.5 focus:ring-luxury-gold shadow-sm">
                        <option value="">-- Choose Category --</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Price (IDR)</label>
                    <input type="number" wire:model="price" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3.5 focus:ring-luxury-gold shadow-sm">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Stock Units</label>
                    <input type="number" wire:model="stock" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3.5 focus:ring-luxury-gold shadow-sm">
                </div>
            </div>

            <div class="p-8 bg-luxury-cream/20 rounded-[2rem] space-y-6 border border-luxury-gold/10 shadow-inner">
                <div class="flex justify-between items-center">
                    <h3 class="font-serif text-xl text-luxury-gold italic">Technical Excellence</h3>
                    <button type="button" wire:click="addSpec" class="text-[9px] font-bold bg-luxury-dark text-white px-5 py-2.5 rounded-full hover:bg-black transition uppercase tracking-widest">+ Add Manual Row</button>
                </div>

                <div class="space-y-3">
                    @foreach($dynamicSpecs as $index => $spec)
                    <div class="flex gap-4 items-center animate-fadeIn" wire:key="spec-{{ $index }}">
                        <input type="text" wire:model="dynamicSpecs.{{ $index }}.key" placeholder="Material/Type" class="w-1/3 text-[10px] font-bold border-transparent bg-white rounded-xl py-3 uppercase tracking-tighter focus:ring-luxury-gold shadow-sm">
                        <input type="text" wire:model="dynamicSpecs.{{ $index }}.value" placeholder="Detail Value" class="w-full text-sm border-transparent bg-white rounded-xl py-3 focus:ring-luxury-gold shadow-sm italic">
                        <button type="button" wire:click="removeSpec({{ $index }})" class="text-gray-300 hover:text-red-500 transition-colors p-2 text-2xl font-light">&times;</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="p-8 bg-blue-50/20 border border-blue-100 rounded-[2rem] space-y-6 shadow-inner">
                <div class="flex justify-between items-center">
                    <h3 class="font-serif text-xl text-blue-900 italic text-opacity-70">Marketing Assets</h3>
                    <button type="button" wire:click="addMarketing" class="text-[9px] font-bold bg-blue-900 bg-opacity-80 text-white px-5 py-2.5 rounded-full hover:bg-blue-900 transition uppercase tracking-widest">+ Add Asset</button>
                </div>

                <div class="space-y-4">
                    @foreach($dynamicMarketing as $index => $item)
                    <div class="flex gap-4 items-center" wire:key="mkt-{{ $index }}">
                        <input type="text" wire:model="dynamicMarketing.{{ $index }}.key" placeholder="Platform/Asset" class="w-1/3 text-[10px] font-bold border-transparent bg-white rounded-xl py-3 uppercase tracking-tighter focus:ring-blue-400 shadow-sm">
                        <textarea wire:model="dynamicMarketing.{{ $index }}.value" placeholder="Description or URL" class="w-full text-sm border-transparent bg-white rounded-xl py-3 focus:ring-blue-400 shadow-sm italic" rows="1"></textarea>
                        <button type="button" wire:click="removeMarketing({{ $index }})" class="text-gray-300 hover:text-red-500 transition-colors p-2 text-2xl font-light">&times;</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" wire:loading.attr="disabled" class="w-full bg-luxury-dark text-white py-6 rounded-2xl font-serif text-2xl shadow-2xl hover:bg-black hover:translate-y-[-4px] transition-all duration-500 flex justify-center items-center gap-4 group">
                    <span>Publish Masterpiece</span>
                    <svg wire:loading.remove class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    <svg wire:loading class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>
