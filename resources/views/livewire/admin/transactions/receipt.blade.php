<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

new class extends Component {
    // Deklarasikan variabel yang akan digunakan di Blade
    public $transaction;
    public $show_logo = true;
    public $header_note = 'Official Masterpiece Certificate';
    public $footer_note = 'Thank you for investing in excellence.';


    public function mount($id) {
        // Ini akan mencari transaksi berdasarkan ID yang dikirim dari Route
        $this->transaction = Transaction::findOrFail($id);

        $settings = $this->transaction->receipt_settings ?? [];
        if (!empty($settings)) {
            $this->show_logo = $settings['show_logo'] ?? true;
            $this->header_note = $settings['header_note'] ?? $this->header_note;
            $this->footer_note = $settings['footer_note'] ?? $this->footer_note;
        }
    }

    public function saveSettings() {
        $this->transaction->update([
            'receipt_settings' => [
                'show_logo' => $this->show_logo,
                'header_note' => $this->header_note,
                'footer_note' => $this->footer_note,
            ]
        ]);
        session()->flash('message', 'Luxury Style Updated!');
    }

    // Paksa kirim variabel ke template Blade di bawah
    public function with() {
        return [
            'transaction' => $this->transaction,
        ];
    }
}; ?>

<div class="grid grid-cols-12 gap-8 p-10 bg-gray-50 min-h-screen">

    <div class="col-span-12 lg:col-span-4 space-y-6 no-print">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h3 class="font-serif text-xl mb-6 border-l-4 border-luxury-gold pl-4 italic">Receipt Tailoring</h3>

            @if (session()->has('message'))
                <div class="mb-4 p-3 bg-green-50 text-green-600 text-[10px] uppercase font-bold tracking-widest rounded-xl border border-green-100">
                    {{ session('message') }}
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Header Note</label>
                    <input type="text" wire:model.live="header_note" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-luxury-gold">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Footer Note</label>
                    <textarea wire:model.live="footer_note" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-luxury-gold" rows="3"></textarea>
                </div>

                <div class="flex items-center gap-3 py-2">
                    <input type="checkbox" wire:model.live="show_logo" id="logo_toggle" class="rounded text-luxury-gold focus:ring-luxury-gold">
                    <label for="logo_toggle" class="text-xs font-medium text-luxury-dark uppercase cursor-pointer">Display Brand Identity</label>
                </div>

                <button wire:click="saveSettings" class="w-full bg-luxury-gold/10 text-luxury-gold py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-luxury-gold hover:text-white transition-all">
                    Commit Changes
                </button>

                <hr class="border-gray-50 my-4">

                <a href="{{ route('admin.transactions.download', $transaction->id) }}" target="_blank" class="block w-full text-center bg-luxury-dark text-white py-4 rounded-xl font-serif text-lg shadow-lg hover:bg-black transition-all">
                    Download PDF
                </a>

                <button onclick="window.print()" class="w-full border border-gray-200 text-gray-500 py-3 rounded-xl font-serif hover:bg-gray-50 transition-all text-sm mt-2">
                    Print to Paper
                </button>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-8 flex justify-center">
        <div id="printable-receipt" class="bg-white w-[450px] p-12 shadow-2xl border border-gray-100 print:shadow-none print:border-none print:w-full">

            <div class="text-center space-y-4 mb-10">
                @if($show_logo)
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" class="h-16 w-auto object-contain">
                    </div>
                @endif
                <h2 class="font-serif text-2xl uppercase tracking-[0.2em] text-luxury-dark">SERO OFFICIAL STORE</h2>
                <p class="text-[10px] text-gray-400 uppercase tracking-[0.4em] font-medium">{{ $header_note }}</p>

                <div class="flex justify-between border-y border-dashed border-gray-200 py-4 mt-6">
                    <span class="text-[10px] font-mono text-left uppercase text-gray-400">#{{ $transaction->reference_no }}</span>
                    <span class="text-[10px] font-mono text-right uppercase text-gray-400">{{ $transaction->created_at->format('d/M/Y H:i') }}</span>
                </div>
            </div>

            <div class="space-y-6 mb-12">
                @foreach($transaction->items as $item)
                    <div class="flex justify-between items-start">
                        <div class="max-w-[70%] text-left">
                            <p class="text-xs font-bold text-luxury-dark uppercase">{{ $item['name'] }}</p>
                            <p class="text-[9px] text-gray-400 italic">Qty: {{ $item['qty'] ?? 1 }} x Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-xs font-mono font-bold text-right text-luxury-dark">Rp {{ number_format(($item['qty'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="border-t-2 border-luxury-dark pt-6">
                <div class="flex justify-between items-center">
                    <span class="font-serif text-lg italic text-luxury-dark">Total Amount</span>
                    <span class="font-serif text-2xl font-bold text-luxury-gold">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-20 text-center">
                <div class="flex justify-center mb-8 opacity-20 grayscale">
                    {!! QrCode::size(60)->generate($transaction->reference_no) !!}
                </div>
                <p class="text-[11px] font-serif italic text-gray-500 leading-relaxed px-8">"{{ $footer_note }}"</p>
                <div class="mt-12 pt-8 border-t border-gray-100">
                    <p class="text-[9px] text-gray-300 uppercase tracking-[0.3em] font-bold">Authorized by Sero Official Archive</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body { background: white !important; }
            body * { visibility: hidden; }
            #printable-receipt, #printable-receipt * { visibility: visible; }
            #printable-receipt { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
            .no-print { display: none !important; }
        }
    </style>
</div>
