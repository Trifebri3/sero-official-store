<?php

use Livewire\Volt\Component;
use App\Models\{MarketingContent, ContentTemplate, Product};
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $filterPlatform = '';
    public $filterProduct = '';

    public function updatingSearch() { $this->resetPage(); }

    // Fungsi Update Status Cepat dari Index
    public function updateStatus($id, $newStatus) {
        $content = MarketingContent::find($id);
        if ($content) {
            $content->update(['status' => $newStatus]);
        }
    }

    public function deleteContent($id) {
        MarketingContent::destroy($id);
        session()->flash('message', 'Aset konten berhasil dihapus.');
    }

    public function with() {
        return [
            'contents' => MarketingContent::with(['product', 'template'])
                ->when($this->search, function($q) {
                    $q->where('title', 'like', '%'.$this->search.'%');
                })
                ->when($this->filterPlatform, function($q) {
                    $q->whereHas('template', fn($query) => $query->where('platform', $this->filterPlatform));
                })
                ->when($this->filterProduct, function($q) {
                    $q->where('product_id', $this->filterProduct);
                })
                ->latest()
                ->paginate(12), // Lebih banyak item per halaman karena desain lebih hemat ruang
            'products' => Product::select('id', 'name')->get(),
            'platforms' => ContentTemplate::distinct()->pluck('platform')
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="bg-white/60 backdrop-blur-md p-4 rounded-[2rem] shadow-sm border border-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative w-full max-w-xs">
                <input type="text" wire:model.live="search" placeholder="Search Campaign..."
                       class="w-full bg-white border-none rounded-xl py-2 pl-10 text-[10px] font-bold uppercase tracking-widest focus:ring-luxury-gold shadow-sm">
                <svg class="w-3 h-3 absolute left-4 top-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3"/></svg>
            </div>

            <select wire:model.live="filterPlatform" class="bg-white border-none rounded-xl py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-luxury-gold shadow-sm px-4">
                <option value="">Platform</option>
                @foreach($platforms as $p) <option value="{{ $p }}">{{ strtoupper($p) }}</option> @endforeach
            </select>

            <select wire:model.live="filterProduct" class="bg-white border-none rounded-xl py-2 text-[10px] font-bold uppercase tracking-widest focus:ring-luxury-gold shadow-sm px-4">
                <option value="">Product</option>
                @foreach($products as $prod) <option value="{{ $prod->id }}">{{ $prod->name }}</option> @endforeach
            </select>
        </div>

        <a href="{{ route('admin.marketing.studio') }}" class="bg-luxury-dark text-white px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] hover:bg-black transition-all shadow-md">
            + New Content
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($contents as $item)
        <div class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm hover:shadow-luxury hover:-translate-y-1 transition-all duration-300 flex items-center gap-5 group">

            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0 group-hover:bg-luxury-gold/10 transition-colors">
                <span class="text-[10px] font-black text-luxury-gold uppercase tracking-tighter">{{ substr($item->template->platform, 0, 2) }}</span>
            </div>

            <div class="flex-grow min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[8px] font-black text-luxury-gold uppercase tracking-[0.2em]">{{ $item->template->platform }}</span>
                    <span class="text-[8px] text-gray-300 font-mono">#{{ $item->id }}</span>
                </div>
                <h3 class="text-xs font-black text-luxury-dark uppercase tracking-tight truncate group-hover:text-luxury-gold transition-colors">
                    {{ $item->title }}
                </h3>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter truncate mt-0.5">{{ $item->product->name }}</p>
            </div>

            <div class="flex flex-col items-end gap-3 flex-shrink-0">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-1 bg-gray-50 rounded-full hover:bg-gray-100 transition-colors">
                        <div class="w-1.5 h-1.5 rounded-full
                            {{ $item->status == 'posted' ? 'bg-emerald-500' : '' }}
                            {{ $item->status == 'scheduled' ? 'bg-luxury-gold shadow-[0_0_5px_rgba(180,150,90,0.5)]' : '' }}
                            {{ $item->status == 'draft' ? 'bg-gray-300' : '' }}">
                        </div>
                        <span class="text-[8px] font-black uppercase text-gray-500">{{ $item->status }}</span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         class="absolute bottom-full mb-2 right-0 w-28 bg-white rounded-xl shadow-2xl border border-gray-100 py-1 z-30">
                        @foreach(['draft', 'scheduled', 'posted'] as $st)
                            <button wire:click="updateStatus({{ $item->id }}, '{{ $st }}')" @click="open = false"
                                    class="w-full text-left px-4 py-2 text-[8px] font-bold uppercase tracking-widest hover:bg-gray-50 {{ $item->status == $st ? 'text-luxury-gold' : 'text-gray-400' }}">
                                {{ $st }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="$dispatch('openContentDetail', { id: {{ $item->id }} })"
                            class="text-[9px] font-black text-luxury-dark hover:text-luxury-gold uppercase tracking-widest">
                        Detail
                    </button>
                    <button onclick="confirm('Hapus?') || event.stopImmediatePropagation()"
                            wire:click="deleteContent({{ $item->id }})"
                            class="text-[9px] font-black text-red-300 hover:text-red-500 uppercase tracking-widest">
                        ×
                    </button>
                </div>
            </div>
        </div>
        @empty
        @endforelse
    </div>

    <div class="mt-8">
        {{ $contents->links() }}
    </div>

    <livewire:admin.marketing.detail />
</div>
