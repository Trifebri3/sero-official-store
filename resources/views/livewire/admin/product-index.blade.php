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
            'products' => $query->latest()->paginate(12),
            'categories' => Category::all(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-10">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="space-y-2">
            <h2 class="font-serif text-4xl text-luxury-dark italic leading-tight">Curated Collection</h2>
            <p class="text-xs text-gray-400 font-bold tracking-[0.3em] uppercase">Manage your high-end inventory</p>
        </div>

        <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
            <div class="relative flex-grow md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
                </span>
                <input type="text" wire:model.live="search" placeholder="Find masterpiece..."
                       class="pl-10 w-full border-gray-100 bg-white rounded-xl py-2.5 text-xs focus:ring-luxury-gold focus:border-luxury-gold shadow-sm transition-all">
            </div>

            <select wire:model.live="category_filter"
                    class="border-gray-100 bg-white rounded-xl py-2.5 px-4 text-xs text-gray-500 focus:ring-luxury-gold shadow-sm cursor-pointer">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <a href="{{ route('admin.products.create') }}"
               class="bg-luxury-dark text-white px-6 py-2.5 rounded-xl font-serif text-sm hover:bg-black transition-all shadow-lg flex items-center gap-2">
                <span>+ Create New</span>
            </a>
        </div>
    </header>

    @if($products->isEmpty())
        <div class="py-40 text-center bg-white rounded-3xl border border-dashed border-gray-200 shadow-luxury">
            <div class="font-serif text-2xl text-gray-300 italic">No masterpieces found in this selection.</div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($products as $product)
                <div class="group relative bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-luxury transition-all duration-500 flex flex-col h-full">

                    <div class="absolute top-4 right-4 z-10 flex flex-col gap-2 translate-x-12 group-hover:translate-x-0 transition-transform duration-300">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2.5 bg-white/90 backdrop-blur shadow-sm rounded-full text-blue-500 hover:bg-blue-500 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.5 2.5 0 113.536 3.536L12.5 21.207l-4 1 1-4L19.586 3.586z" stroke-width="2"/></svg>
                        </a>
                        <button wire:click="delete({{ $product->id }})"
                                wire:confirm="Remove this masterpiece from vault?"
                                class="p-2.5 bg-white/90 backdrop-blur shadow-sm rounded-full text-red-400 hover:bg-red-500 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                        </button>
                    </div>

                    <a href="{{ route('admin.products.show', $product->id) }}" class="relative aspect-[4/5] bg-gray-50 overflow-hidden">
                        @php $firstImage = $product->media[0] ?? null; @endphp
                        <img src="{{ $firstImage ? Storage::url($firstImage) : 'https://placehold.co/600x800/FDFBF9/B4965A?text=No+Visual' }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 grayscale-[0.2] group-hover:grayscale-0">

                        <div class="absolute bottom-4 left-4">
                            @if($product->stock <= 0)
                                <span class="px-3 py-1 bg-red-500 text-white text-[9px] font-bold uppercase tracking-widest rounded-full shadow-lg">Sold Out</span>
                            @elseif($product->stock < 5)
                                <span class="px-3 py-1 bg-luxury-gold text-white text-[9px] font-bold uppercase tracking-widest rounded-full shadow-lg animate-pulse">Limited Reserve</span>
                            @else
                                <span class="px-3 py-1 bg-white/90 backdrop-blur text-luxury-dark text-[9px] font-bold uppercase tracking-widest rounded-full shadow-lg">In Vault</span>
                            @endif
                        </div>
                    </a>

                    <div class="p-6 flex flex-col flex-grow bg-white">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[9px] font-bold text-luxury-gold uppercase tracking-[0.2em]">{{ $product->category->name ?? 'Collection' }}</span>
                            <span class="text-[9px] font-mono text-gray-300">#{{ $product->sku }}</span>
                        </div>

                        <a href="{{ route('admin.products.show', $product->id) }}" class="block mb-4">
                            <h3 class="font-serif text-lg text-luxury-dark group-hover:text-luxury-gold transition-colors italic leading-tight">{{ $product->name }}</h3>
                        </a>

                        <div class="mt-auto space-y-4">
                            <div class="flex items-end justify-between border-t border-gray-50 pt-4">
                                <div class="flex flex-col">
                                    <span class="text-[8px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1">Valuation</span>
                                    <span class="font-serif text-xl text-luxury-dark leading-none">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[8px] text-gray-400 uppercase font-bold tracking-widest leading-none mb-1 d-block">Available</span>
                                    <span class="text-xs font-bold text-luxury-dark">{{ $product->stock }} <small class="text-[10px] text-gray-300 font-normal italic">units</small></span>
                                </div>
                            </div>

                            <a href="{{ route('admin.products.show', $product->id) }}"
                               class="w-full flex justify-center items-center py-2 text-[10px] font-bold text-gray-400 hover:text-luxury-dark border-t border-gray-50 uppercase tracking-[0.3em] transition-all">
                                Inspect Masterpiece
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-10">
            {{ $products->links() }}
        </div>
    @endif
</div>
