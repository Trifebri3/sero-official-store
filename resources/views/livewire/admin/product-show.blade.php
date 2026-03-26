<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public Product $product;

    public function mount($id)
    {
        // Eager load category untuk performa maksimal
        $this->product = Product::with('category')->findOrFail($id);
    }
}; ?>

<div class="min-h-screen bg-[#FDFBF9] pb-20">
    <div class="bg-white border-b border-luxury-cream sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.products.index') }}" class="p-2 hover:bg-gray-50 rounded-full transition text-gray-400 hover:text-luxury-dark" wire:navigate>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <div class="text-[10px] text-luxury-gold font-bold uppercase tracking-[0.3em] leading-none mb-1">{{ $product->sku }}</div>
                    <h1 class="font-serif text-2xl text-luxury-dark italic leading-none">{{ $product->name }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.edit', $product->id) }}" wire:navigate
                   class="bg-luxury-dark text-white px-8 py-2.5 rounded-full font-serif text-sm hover:bg-black transition-all shadow-lg hover:shadow-xl">
                    Edit Masterpiece
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 mt-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <div class="lg:col-span-2 space-y-10">

                <section class="bg-white p-8 rounded-3xl shadow-luxury border border-gray-100">
                    <h3 class="font-serif text-xl text-luxury-dark mb-6 flex items-center gap-3 italic font-light">
                        <span class="w-8 h-[1px] bg-luxury-gold"></span> Visual Gallery
                    </h3>

                    @if($product->media && count($product->media) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($product->media as $image)
                                <div class="aspect-[4/5] overflow-hidden rounded-2xl border border-gray-100 group bg-gray-50">
                                    <img src="{{ Storage::url($image) }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000"
                                         alt="Masterpiece Visual"
                                         onerror="this.src='https://placehold.co/600x800/FDFBF9/B4965A?text=Image+Not+Found'">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-24 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                            <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1"/></svg>
                            <p class="text-gray-400 font-serif italic">No curated visuals found for this item.</p>
                        </div>
                    @endif
                </section>

                <section class="bg-white p-8 rounded-3xl shadow-luxury border border-gray-100 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <svg class="w-32 h-32 text-luxury-dark" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>

                    <h3 class="font-serif text-xl text-luxury-dark mb-8 flex items-center gap-3 italic">
                        <span class="w-8 h-[1px] bg-luxury-gold"></span> Activity & Modification Logs
                    </h3>

                    <div class="space-y-8 relative">
                        <div class="absolute left-[11px] top-2 bottom-2 w-[1px] bg-gray-100"></div>

                        <div class="relative flex gap-6 items-start">
                            <div class="mt-1.5 w-[22px] h-[22px] rounded-full bg-white border-2 border-luxury-gold flex items-center justify-center z-10 shadow-sm">
                                <div class="w-1.5 h-1.5 rounded-full bg-luxury-gold"></div>
                            </div>
                            <div class="flex-1 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-xs font-bold text-luxury-dark uppercase tracking-widest">Masterpiece Commissioned</h4>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $product->created_at->format('H:i T') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 italic leading-relaxed">Produk pertama kali didaftarkan ke dalam sistem inventaris eksklusif dengan SKU {{ $product->sku }}.</p>
                                <div class="mt-3 text-[9px] text-luxury-gold font-bold uppercase">{{ $product->created_at->format('d F, Y') }}</div>
                            </div>
                        </div>

                        @if($product->updated_at != $product->created_at)
                        <div class="relative flex gap-6 items-start">
                            <div class="mt-1.5 w-[22px] h-[22px] rounded-full bg-white border-2 border-blue-400 flex items-center justify-center z-10 shadow-sm">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></div>
                            </div>
                            <div class="flex-1 bg-blue-50/30 p-4 rounded-2xl border border-blue-100">
                                <div class="flex justify-between items-start mb-1">
                                    <h4 class="text-xs font-bold text-blue-900 uppercase tracking-widest">Curation Updated</h4>
                                    <span class="text-[10px] text-blue-400 font-mono">{{ $product->updated_at->format('H:i T') }}</span>
                                </div>
                                <p class="text-xs text-blue-800/70 italic leading-relaxed">Terjadi perubahan pada detail spesifikasi atau aset pemasaran untuk meningkatkan nilai presentasi.</p>
                                <div class="mt-3 text-[9px] text-blue-500 font-bold uppercase">{{ $product->updated_at->format('d F, Y') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="space-y-8">

                <div class="bg-white p-8 rounded-3xl shadow-luxury border border-luxury-cream text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-luxury-gold opacity-0 group-hover:opacity-[0.02] transition-opacity"></div>
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.4em] mb-6">Authenticity Code</h4>

                    <div class="inline-block py-4 px-6 bg-[#FDFBF9] border border-luxury-cream rounded-xl mb-4">
                        <span class="text-2xl font-mono font-light tracking-tighter text-luxury-dark">
                            {{ strtoupper(substr(md5($product->id), 0, 4)) }}-{{ $product->id }}-{{ date('y') }}
                        </span>
                    </div>

                    <p class="text-[10px] text-luxury-gold font-medium uppercase tracking-[0.2em]">Verified Masterpiece Identity</p>
                </div>

                <div class="bg-luxury-dark p-8 rounded-[2rem] shadow-2xl text-white relative overflow-hidden">
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-luxury-gold/20 rounded-full blur-[60px]"></div>
                    <h4 class="text-[10px] font-bold tracking-[0.4em] text-luxury-gold uppercase mb-6 opacity-80">Current Valuation</h4>

                    <div class="flex items-baseline gap-1 mb-2">
                        <span class="text-luxury-gold font-serif italic text-lg">Rp</span>
                        <span class="text-5xl font-serif italic leading-none">{{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center gap-3 mt-8 pt-6 border-t border-white/10">
                        <div class="h-2 w-2 rounded-full {{ $product->stock > 0 ? 'bg-green-400 shadow-[0_0_10px_rgba(74,222,128,0.5)]' : 'bg-red-400' }} animate-pulse"></div>
                        <span class="text-[10px] text-gray-400 uppercase tracking-[0.2em]">Stock: <b class="text-white">{{ $product->stock }} Units</b></span>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-luxury border border-gray-100">
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 border-b pb-4 border-gray-50">Technical Excellence</h4>
                    <div class="space-y-4">
                        @forelse($product->specifications ?? [] as $key => $value)
                            <div class="flex flex-col gap-1 border-b border-gray-50 pb-3 last:border-0">
                                <span class="text-[9px] font-bold text-gray-300 uppercase tracking-tighter">{{ str_replace('_', ' ', $key) }}</span>
                                <span class="text-sm font-serif italic text-luxury-dark">{{ $value ?: '—' }}</span>
                            </div>
                        @empty
                            <p class="text-xs italic text-gray-400">Specifications not yet detailed.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-blue-950 p-8 rounded-3xl shadow-xl text-white">
                    <h4 class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-6 italic">Marketing Strategy</h4>
                    <div class="space-y-6">
                        @forelse($product->marketing_assets ?? [] as $key => $value)
                            <div class="space-y-2">
                                <p class="text-[9px] font-bold text-blue-300/50 uppercase tracking-widest">{{ $key }}</p>
                                <p class="text-xs text-blue-50 leading-relaxed font-serif italic opacity-90">"{{ $value }}"</p>
                            </div>
                        @empty
                            <p class="text-xs italic text-blue-300/50 text-center py-4">No assets defined.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
