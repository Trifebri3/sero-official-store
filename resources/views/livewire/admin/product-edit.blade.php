<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Product $product;

    // Properti Form Dasar
    public string $name = '';
    public $price = 0;
    public $category_id = '';
    public int $stock = 0;

    // Properti Media
    public $new_photos = []; // Untuk upload foto baru
    public array $existing_media = []; // Untuk nampilin foto yang sudah ada di DB

    // Properti JSON Dinamis
    public array $dynamicSpecs = [];
    public array $dynamicMarketing = [];

    public function mount($id)
    {
        $this->product = Product::findOrFail($id);

        $this->name = $this->product->name;
        $this->price = $this->product->price;
        $this->category_id = $this->product->category_id;
        $this->stock = $this->product->stock;
        $this->existing_media = $this->product->media ?? [];

        // Map specifications
        if ($this->product->specifications) {
            foreach ($this->product->specifications as $key => $value) {
                $this->dynamicSpecs[] = ['key' => $key, 'value' => $value];
            }
        }

        // Map marketing assets
        if ($this->product->marketing_assets) {
            foreach ($this->product->marketing_assets as $key => $value) {
                $this->dynamicMarketing[] = ['key' => $key, 'value' => $value];
            }
        }

        if (empty($this->dynamicSpecs)) $this->dynamicSpecs = [['key' => '', 'value' => '']];
        if (empty($this->dynamicMarketing)) $this->dynamicMarketing = [['key' => '', 'value' => '']];
    }

    public function updatedCategoryId($id)
    {
        $category = Category::find($id);
        if ($category) {
            $this->dynamicSpecs = collect($category->default_specs)
                ->map(fn($item) => ['key' => $item, 'value' => ''])
                ->toArray();
        }
    }

    // Logic Hapus Foto (Lama & Baru)
    public function removeExistingMedia($index)
    {
        // Hapus file fisik (opsional, tergantung kebijakan storage kamu)
        // Storage::disk('public')->delete($this->existing_media[$index]);

        unset($this->existing_media[$index]);
        $this->existing_media = array_values($this->existing_media);
    }

    public function removeNewPhoto($index)
    {
        array_splice($this->new_photos, $index, 1);
    }

    // Aksi Dinamis Row
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

    public function update()
    {
        $this->validate([
            'name' => 'required|min:5',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|integer',
            'new_photos.*' => 'nullable|image|max:5120',
        ]);

        // 1. Handle Foto Baru
        $updatedMedia = $this->existing_media;
        if ($this->new_photos) {
            foreach ($this->new_photos as $photo) {
                $updatedMedia[] = $photo->store('products', 'public');
            }
        }

        // 2. Re-mapping JSON
        $finalSpecs = collect($this->dynamicSpecs)->pluck('value', 'key')->filter(fn($v, $k) => !empty($k))->toArray();
        $finalMarketing = collect($this->dynamicMarketing)->pluck('value', 'key')->filter(fn($v, $k) => !empty($k))->toArray();

        // 3. Update Database
        $this->product->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'specifications' => $finalSpecs,
            'marketing_assets' => $finalMarketing,
            'media' => $updatedMedia,
        ]);

        session()->flash('message', 'Masterpiece successfully updated!');
        $this->redirect(route('admin.products.index'), navigate: true);
    }
}; ?>

<div class="p-8 bg-white shadow-luxury rounded-2xl border border-gray-100 max-w-5xl mx-auto my-10">
    <div class="mb-10 flex justify-between items-end border-b pb-8 border-luxury-cream">
        <div class="space-y-1">
            <span class="inline-block px-3 py-1 bg-luxury-gold/10 text-luxury-gold text-[10px] font-bold tracking-[0.3em] rounded-full uppercase mb-2">
                Serial: {{ $product->sku }}
            </span>
            <h2 class="font-serif text-4xl text-luxury-dark italic capitalize leading-tight">Edit Masterpiece</h2>
        </div>
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-luxury-dark transition-all uppercase tracking-widest">
            Back to Inventory
        </a>
    </div>

    <form wire:submit="update" class="space-y-10">

        <section class="space-y-6">
            <div class="flex justify-between items-center">
                <h4 class="font-serif text-xl text-luxury-gold italic border-l-4 border-luxury-gold pl-4">Media Gallery</h4>
                <div wire:loading wire:target="new_photos" class="text-[10px] text-luxury-gold animate-pulse uppercase font-bold">Uploading New Assets...</div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($existing_media as $index => $path)
                    <div class="relative group aspect-square rounded-xl overflow-hidden border border-luxury-cream">
                        <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover grayscale-[0.5] group-hover:grayscale-0 transition-all">
                        <button type="button" wire:click="removeExistingMedia({{ $index }})" class="absolute top-2 right-2 bg-black/50 text-white p-1 rounded-full hover:bg-red-500 transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                @endforeach

                @foreach($new_photos as $index => $photo)
                    <div class="relative group aspect-square rounded-xl overflow-hidden border-2 border-dashed border-luxury-gold">
                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        <button type="button" wire:click="removeNewPhoto({{ $index }})" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                @endforeach

                <label class="flex flex-col items-center justify-center aspect-square border-2 border-dashed border-gray-200 rounded-xl hover:border-luxury-gold hover:bg-luxury-cream/20 transition cursor-pointer group">
                    <svg class="w-8 h-8 text-gray-300 group-hover:text-luxury-gold transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/></svg>
                    <span class="text-[9px] font-bold text-gray-400 mt-2 uppercase text-center px-2">Add New Masterpiece Visual</span>
                    <input type="file" wire:model="new_photos" multiple class="hidden">
                </label>
            </div>
        </section>

        <section class="space-y-6">
            <h4 class="font-serif text-xl text-luxury-gold border-l-4 border-luxury-gold pl-4 italic">General Identity</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Full Product Name</label>
                    <input type="text" wire:model="name" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3 focus:ring-luxury-gold shadow-sm transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Category Template</label>
                    <select wire:model.live="category_id" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3 focus:ring-luxury-gold shadow-sm">
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Retail Price (IDR)</label>
                    <input type="number" wire:model="price" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3 focus:ring-luxury-gold shadow-sm font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-luxury-dark uppercase tracking-widest">Stock Units</label>
                    <input type="number" wire:model="stock" class="w-full border-gray-100 bg-gray-50/50 rounded-xl py-3 focus:ring-luxury-gold shadow-sm">
                </div>
            </div>
        </section>

        <section class="p-8 bg-luxury-cream/30 rounded-2xl border border-luxury-gold/10 space-y-6">
            <div class="flex justify-between items-center">
                <h4 class="font-serif text-xl text-luxury-gold italic">Technical Specifications</h4>
                <button type="button" wire:click="addSpec" class="text-[9px] bg-luxury-dark text-white px-4 py-2 rounded-full hover:bg-black transition-all font-bold tracking-widest uppercase">+ Add Row</button>
            </div>
            <div class="space-y-4">
                @foreach($dynamicSpecs as $index => $spec)
                <div class="flex gap-4 items-center group" wire:key="spec-{{ $index }}">
                    <input type="text" wire:model="dynamicSpecs.{{ $index }}.key" placeholder="Key" class="w-1/3 text-xs font-bold border-transparent bg-white/70 rounded-lg py-2 uppercase focus:ring-luxury-gold">
                    <input type="text" wire:model="dynamicSpecs.{{ $index }}.value" placeholder="Value" class="w-full text-sm border-transparent bg-white/70 rounded-lg py-2 focus:ring-luxury-gold italic">
                    <button type="button" wire:click="removeSpec({{ $index }})" class="text-red-300 hover:text-red-500 transition-colors px-2 text-2xl">&times;</button>
                </div>
                @endforeach
            </div>
        </section>

        <section class="p-8 bg-blue-50/20 rounded-2xl border border-blue-100 space-y-6">
            <div class="flex justify-between items-center">
                <h4 class="font-serif text-xl text-blue-800 italic">Marketing Assets</h4>
                <button type="button" wire:click="addMarketing" class="text-[9px] bg-blue-800 text-white px-4 py-2 rounded-full hover:bg-blue-900 transition-all font-bold tracking-widest uppercase">+ Add Asset</button>
            </div>
            <div class="space-y-4">
                @foreach($dynamicMarketing as $index => $item)
                <div class="flex gap-4 items-start group" wire:key="mkt-{{ $index }}">
                    <input type="text" wire:model="dynamicMarketing.{{ $index }}.key" class="w-1/3 text-xs font-bold border-transparent bg-white/70 rounded-lg py-2 uppercase focus:ring-blue-400">
                    <textarea wire:model="dynamicMarketing.{{ $index }}.value" class="w-full text-sm border-transparent bg-white/70 rounded-lg py-2 focus:ring-blue-400" rows="1"></textarea>
                    <button type="button" wire:click="removeMarketing({{ $index }})" class="text-red-300 hover:text-red-500 transition-colors px-2 text-2xl">&times;</button>
                </div>
                @endforeach
            </div>
        </section>

        <div class="pt-10 flex flex-col items-center">
            <button type="submit" class="group relative w-full bg-luxury-dark text-white py-6 rounded-2xl font-serif text-2xl shadow-2xl overflow-hidden transition-all hover:translate-y-[-4px]">
                <span class="relative z-10">Commit Perubahan Masterpiece</span>
                <div class="absolute inset-0 bg-luxury-gold translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
            </button>
        </div>
    </form>
</div>
