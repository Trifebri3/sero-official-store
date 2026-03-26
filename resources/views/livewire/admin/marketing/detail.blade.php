<?php

use Livewire\Volt\Component;
use App\Models\MarketingContent;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    public $contentId;
    public $isOpen = false;

    protected $listeners = ['openContentDetail' => 'loadContent'];

    public function loadContent($id)
    {
        $this->contentId = $id;
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function with()
    {
        return [
            // Kita load product beserta category dan media agar lengkap sesuai referensi inventory
            'content' => $this->contentId ? MarketingContent::with(['product.category', 'template'])->find($this->contentId) : null
        ];
    }

    // Tambahkan di dalam class component Volt tadi:

public function updateStatus($newStatus)
{
    $content = MarketingContent::find($this->contentId);
    if ($content) {
        $content->update(['status' => $newStatus]);
        session()->flash('message', 'Status updated to ' . strtoupper($newStatus));
    }
}



}; ?>

<div>
    @if($isOpen && $content)
    <div class="fixed inset-0 z-[60] flex justify-end transition-all duration-500">
        <div wire:click="close" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-2xl bg-[#FDFBF9] h-full shadow-2xl overflow-y-auto animate-in slide-in-from-right duration-500 border-l border-luxury-gold/20">

            <button wire:click="close" class="absolute top-8 right-8 z-20 p-2 bg-white rounded-full text-gray-400 hover:text-luxury-dark shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
            </button>

            <div class="p-10">
                <div class="mb-10 pt-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-3 py-1 bg-luxury-dark text-luxury-gold text-[9px] font-black uppercase tracking-[0.2em] rounded-full">
                            {{ $content->template->platform }} Platform
                        </span>
                        <span class="text-[9px] font-mono text-gray-300">ID: #{{ $content->id }}</span>
                    </div>
                    <h2 class="text-3xl font-serif italic text-luxury-dark leading-tight">{{ $content->title }}</h2>
                </div>
<div class="flex items-center gap-4 mb-10 p-4 bg-white rounded-2xl border border-gray-50 shadow-sm">
    <span class="text-[8px] font-bold text-gray-400 uppercase tracking-widest ml-2">Execution Status:</span>

    <div class="flex bg-gray-50 p-1 rounded-xl w-full">
        @foreach(['draft', 'scheduled', 'posted'] as $st)
            <button wire:click="updateStatus('{{ $st }}')"
                class="flex-1 py-2 text-[9px] font-black uppercase tracking-tighter rounded-lg transition-all
                {{ $content->status == $st
                    ? 'bg-luxury-dark text-luxury-gold shadow-lg scale-105'
                    : 'text-gray-300 hover:text-luxury-dark' }}">
                {{ $st }}
            </button>
        @endforeach
    </div>
</div>
                <div class="mb-12 bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="text-[8px] font-bold text-luxury-gold uppercase tracking-[0.3em] block mb-1">{{ $content->product->category->name ?? 'Collection' }}</span>
                            <h3 class="font-serif text-xl text-luxury-dark italic">{{ $content->product->name }}</h3>
                        </div>
                        <div class="text-right">
                            <span class="text-[8px] font-bold text-gray-300 uppercase tracking-widest block mb-1">Valuation</span>
                            <span class="font-serif text-lg text-luxury-dark">Rp{{ number_format($content->product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 py-4 border-t border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[8px] text-gray-400 uppercase font-bold tracking-widest mb-1">SKU Masterpiece</span>
                            <span class="text-xs font-mono font-bold text-luxury-dark">{{ $content->product->sku }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[8px] text-gray-400 uppercase font-bold tracking-widest mb-1">Stock status</span>
                            <span class="text-xs font-bold {{ $content->product->stock < 5 ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $content->product->stock }} Units Available
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-12">
                    <h3 class="text-[10px] font-bold text-luxury-dark uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                        <svg class="w-4 h-4 text-luxury-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                        Visual Masterpieces
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        @php $mediaFiles = is_array($content->product->media) ? $content->product->media : []; @endphp

                        @forelse($mediaFiles as $image)
                            <div class="group relative aspect-[4/5] bg-gray-50 rounded-[2rem] overflow-hidden border border-gray-100 shadow-sm">
                                <img src="{{ Storage::url($image) }}" class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-700">

                                <div class="absolute inset-0 bg-luxury-dark/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-6">
                                    <a href="{{ Storage::url($image) }}" download
                                       class="w-full text-center bg-white text-luxury-dark py-3 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform">
                                        Download Visual
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 py-12 text-center bg-gray-50 rounded-[2rem] border border-dashed border-gray-200">
                                <span class="text-[10px] font-bold text-gray-300 uppercase italic">No media assets in vault</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-6">
                    <h3 class="text-[10px] font-bold text-luxury-dark uppercase tracking-[0.3em] mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-luxury-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/></svg>
                        Copywriting Script
                    </h3>

                    @foreach($content->content_data as $key => $value)
                    <div class="relative bg-white p-6 pt-10 rounded-[2rem] border border-gray-100 shadow-sm group">
                        <div class="absolute top-0 left-6 -translate-y-1/2 bg-luxury-gold text-white text-[8px] font-black uppercase tracking-[0.2em] px-4 py-1.5 rounded-full shadow-lg">
                            {{ $key }}
                        </div>

                        <p id="copy-{{ $loop->index }}" class="text-[11px] text-gray-600 leading-relaxed pr-10 whitespace-pre-wrap">{{ $value ?: 'No data provided' }}</p>

                        <button onclick="copyToClipboard('copy-{{ $loop->index }}')"
                                class="absolute top-6 right-6 p-2 text-gray-300 hover:text-luxury-gold transition-colors"
                                title="Copy to clipboard">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-width="2"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @endif

    <script>
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                // Kamu bisa ganti alert ini dengan toast luxury nanti
                alert('Copied to clipboard!');
            });
        }
    </script>
</div>
