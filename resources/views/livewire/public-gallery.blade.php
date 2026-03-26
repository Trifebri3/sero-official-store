<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $category_filter = '';
    public ?Product $selectedProduct = null;
    public bool $showModal = false;

    public function openDetail($id)
    {
        $this->selectedProduct = Product::with('category')->find($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
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

<div class="relative w-full overflow-x-hidden">
    <div class="flex flex-col lg:flex-row justify-between items-center gap-8 mb-16 border-b border-black/5 pb-10 px-4">
        <div class="flex flex-wrap justify-center lg:justify-start gap-6 md:gap-10">
            <button wire:click="$set('category_filter', '')"
                class="group flex flex-col items-center gap-2 transition-all">
                <span class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.4em] {{ $category_filter === '' ? 'text-[#C5A358]' : 'text-gray-400 hover:text-black' }}">All Pieces</span>
                <div class="h-[1px] bg-[#C5A358] transition-all duration-500 {{ $category_filter === '' ? 'w-full' : 'w-0 group-hover:w-1/2' }}"></div>
            </button>
            @foreach($categories as $cat)
                <button wire:click="$set('category_filter', '{{ $cat->id }}')"
                    class="group flex flex-col items-center gap-2 transition-all">
                    <span class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.4em] {{ $category_filter == $cat->id ? 'text-[#C5A358]' : 'text-gray-400 hover:text-black' }}">{{ $cat->name }}</span>
                    <div class="h-[1px] bg-[#C5A358] transition-all duration-500 {{ $category_filter == $cat->id ? 'w-full' : 'w-0 group-hover:w-1/2' }}"></div>
                </button>
            @endforeach
        </div>

        <div class="relative w-full md:w-80 group">
            <input type="text" wire:model.live="search" placeholder="Search masterpiece..."
                class="w-full bg-[#FDFBF9] border-none rounded-full px-8 py-4 text-xs font-serif italic shadow-inner focus:ring-1 focus:ring-[#C5A358] transition-all">
            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#C5A358] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
            </div>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="py-40 text-center flex flex-col items-center px-4">
            <div class="w-20 h-[1px] bg-gray-200 mb-8"></div>
            <p class="font-serif italic text-2xl md:text-3xl text-gray-300">The vault is currently empty.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-20 px-4">
            @foreach($products as $product)
                <div class="group cursor-pointer flex flex-col h-full" wire:click="openDetail({{ $product->id }})">
                    <div class="aspect-[3/4] overflow-hidden rounded-[3rem] md:rounded-[4rem] bg-[#F5F2ED] relative shadow-sm border-[6px] md:border-[10px] border-white transition-all duration-1000 group-hover:shadow-2xl group-hover:rounded-[2rem]">
                        @php $mainImage = $product->media[0] ?? $product->image; @endphp
                        <img src="{{ $mainImage ? Storage::url($mainImage) : asset('images/foto1.png') }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[3s] grayscale-[15%] group-hover:grayscale-0"
                             alt="{{ $product->name }}">

                        @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center backdrop-blur-[2px]">
                                <span class="bg-black text-white text-[8px] font-black uppercase tracking-[0.3em] px-8 py-3 rounded-full border border-white/20">Archived</span>
                            </div>
                        @else
                            <div class="absolute bottom-8 left-0 right-0 flex justify-center opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                                <span class="bg-white/95 backdrop-blur-md text-black text-[9px] font-black uppercase tracking-widest px-8 py-3 rounded-full shadow-xl">View Details</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 flex flex-col flex-grow px-2">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[8px] md:text-[9px] font-black text-[#C5A358] uppercase tracking-[0.4em] truncate max-w-[70%]">
                                {{ $product->category->name ?? 'Collection' }}
                            </span>
                            <span class="text-[8px] font-mono text-gray-300">#{{ $product->sku }}</span>
                        </div>

                        <h3 class="font-serif italic text-2xl md:text-3xl text-[#1C1C1C] group-hover:text-[#C5A358] transition-colors duration-500 leading-tight mb-4 line-clamp-2">
                            {{ $product->name }}
                        </h3>

                        <div class="mt-auto pt-4 border-t border-black/5">
                            <p class="font-serif text-xl text-[#1C1C1C]">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-24 flex justify-center pb-10">
            {{ $products->links() }}
        </div>
    @endif

    @if($showModal && $selectedProduct)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-0 md:p-6 lg:p-12 overflow-hidden">
        <div class="absolute inset-0 bg-[#1C1C1C]/98 backdrop-blur-lg" wire:click="closeModal"></div>

        <div class="relative bg-white w-full max-w-7xl h-full md:h-auto md:max-h-[90vh] overflow-y-auto md:rounded-[4rem] shadow-2xl flex flex-col lg:flex-row scrollbar-hide">
            <button wire:click="closeModal" class="absolute top-6 right-6 z-[110] p-3 bg-white/10 backdrop-blur rounded-full lg:bg-transparent text-gray-400 hover:text-black transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
            </button>

            <div class="w-full lg:w-3/5 p-4 md:p-10 bg-[#FDFBF9]">
                <div class="columns-1 md:columns-2 gap-4 space-y-4">
                    @php $allMedia = $selectedProduct->media ?? []; @endphp
                    @forelse($allMedia as $img)
                        <div class="break-inside-avoid rounded-3xl overflow-hidden shadow-sm border-4 border-white hover:shadow-xl transition-all duration-700">
                            <img src="{{ Storage::url($img) }}" class="w-full h-auto object-cover" alt="Gallery">
                        </div>
                    @empty
                        <div class="rounded-[3rem] overflow-hidden">
                            <img src="{{ $selectedProduct->image ? asset('storage/' . $selectedProduct->image) : asset('images/foto1.png') }}" class="w-full h-auto object-cover">
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="w-full lg:w-2/5 p-8 md:p-16 lg:p-20 flex flex-col justify-center bg-white">
                <div class="space-y-10">
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="h-[1px] w-8 bg-[#C5A358]"></div>
                            <span class="text-[10px] font-black text-[#C5A358] uppercase tracking-[0.5em]">{{ $selectedProduct->sku }}</span>
                        </div>
                        <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl italic text-[#1C1C1C] leading-[1.1]">{{ $selectedProduct->name }}</h2>
                        <p class="text-3xl font-serif text-[#1C1C1C] pt-2">IDR {{ number_format($selectedProduct->price, 0, ',', '.') }}</p>
                    </div>

                    <div class="space-y-6 pt-6 border-t border-gray-50">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300">Technical Excellence</h4>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-6">
                            @forelse($selectedProduct->specifications ?? [] as $key => $value)
                                <div class="space-y-1 group">
                                    <p class="text-[8px] text-gray-300 uppercase font-black tracking-widest group-hover:text-[#C5A358] transition-colors">{{ str_replace('_', ' ', $key) }}</p>
                                    <p class="text-sm font-serif italic text-black leading-tight">{{ $value ?: '—' }}</p>
                                </div>
                            @empty
                                <p class="text-xs italic text-gray-400 col-span-2">Details on request.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-8 flex flex-col gap-6">
                        <a href="https://s.shopee.co.id/W2DatWTIn" target="_blank"
                           class="group relative bg-[#1C1C1C] text-white py-6 rounded-full text-center overflow-hidden shadow-2xl transition-all duration-500 hover:bg-black">
                           <span class="relative z-10 text-[11px] font-black uppercase tracking-[0.4em]">Secure this Piece</span>
                           <div class="absolute inset-0 bg-[#C5A358] translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                        </a>
                        <div class="flex items-center justify-center gap-4 opacity-40">
                            <div class="h-[1px] flex-grow bg-black"></div>
                            <span class="text-[8px] font-black uppercase tracking-[0.3em] whitespace-nowrap">SERO LIVING AUTHENTIC</span>
                            <div class="h-[1px] flex-grow bg-black"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
