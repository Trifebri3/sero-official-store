<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    public $cart = [];
    public $search = '';
    public $type = 'offline';
    public $payment_method = 'cash';
    public $discount = 0;

    // Customer Info
    public $customer_name = '';
    public $customer_phone = '';

    /**
     * Menambahkan Produk ke Keranjang
     */
public function addToCart($id)
{
    // HAPUS ->with('media') karena media adalah KOLOM, bukan RELASI
    $product = Product::find($id);

    if (!$product || $product->stock <= 0) return;

    if (isset($this->cart[$id])) {
        // Cek apakah stok masih mencukupi untuk ditambah
        if ($this->cart[$id]['qty'] < $product->stock) {
            $this->cart[$id]['qty']++;
        }
    } else {
        // Karena media sudah di-cast sebagai 'array' di Model,
        // kita bisa langsung akses index ke-0
        $image = $product->media[0] ?? null;

        $this->cart[$id] = [
            'id' => $id,
            'name' => $product->name,
            'price' => $product->price,
            'qty' => 1,
            // Jika ada path gambar, buatkan URL publiknya
            'image' => $image ? Storage::url($image) : null,
        ];
    }
}

    public function removeFromCart($id) { unset($this->cart[$id]); }

    public function incrementQty($id) {
        $product = Product::find($id);
        if ($this->cart[$id]['qty'] < $product->stock) {
            $this->cart[$id]['qty']++;
        }
    }

    public function decrementQty($id) {
        if ($this->cart[$id]['qty'] > 1) {
            $this->cart[$id]['qty']--;
        } else {
            $this->removeFromCart($id);
        }
    }

    /**
     * Computed Property untuk Totalan
     */
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal - (float)$this->discount;
    }

    /**
     * Simpan Transaksi ke Database
     */
    public function submit()
    {
        if (empty($this->cart)) return;

        $transaction = Transaction::create([
            'reference_no' => 'INV-' . strtoupper(Str::random(10)),
            'type' => $this->type,
            'channel' => $this->type == 'online' ? 'Marketplace' : 'In-Store',
            'items' => array_values($this->cart),
            'customer_info' => [
                'name' => $this->customer_name ?: 'Guest Customer',
                'phone' => $this->customer_phone
            ],
            'subtotal' => $this->subtotal,
            'discount' => (float)$this->discount,
            'grand_total' => $this->grand_total,
            'payment_method' => $this->payment_method,
            'user_id' => auth()->id(),
            'metadata' => [
                'source' => 'Ranaloka POS Terminal',
                'log_time' => now()->toDateTimeString(),
                'ip' => request()->ip()
            ]
        ]);

        // Kurangi Stok Produk Otomatis
        foreach ($this->cart as $item) {
            Product::find($item['id'])->decrement('stock', $item['qty']);
        }

        session()->flash('success', 'Masterpiece Transaction Secured.');
        return redirect()->route('admin.transactions.history');
    }

// Cari bagian ini di file Volt POS:
public function with()
{
    return [
        'products' => Product::where('name', 'like', "%{$this->search}%") // Hapus ->with('media')
            ->where('stock', '>', 0)
            ->latest()
            ->get()
    ];
}
}; ?>

<div class="grid grid-cols-12 gap-0 min-h-screen bg-[#FDFBF9]">
    <div class="col-span-12 lg:col-span-8 p-8 space-y-8 border-r border-gray-100">
        <header class="flex justify-between items-center">
            <div>
                <h2 class="font-serif text-3xl italic text-luxury-dark">Vault <span class="text-luxury-gold">Terminal</span></h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.3em]">Authorized Personnel Only</p>
            </div>

            <div class="flex gap-4">
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="Search masterpiece..."
                           class="pl-10 w-full border-none bg-white rounded-2xl py-3 text-xs shadow-sm focus:ring-luxury-gold transition-all">
                </div>
            </div>
        </header>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 overflow-y-auto max-h-[75vh] pr-2 custom-scrollbar">
            @foreach($products as $product)
                <button wire:click="addToCart({{ $product->id }})"
                        class="bg-white p-4 rounded-[2rem] border border-transparent hover:border-luxury-gold transition-all duration-500 text-left group shadow-sm hover:shadow-xl relative">

                    <div class="aspect-square bg-gray-50 rounded-[1.5rem] mb-4 overflow-hidden relative">
                        @php $firstImage = $product->media[0] ?? null; @endphp
                        <img src="{{ $firstImage ? Storage::url($firstImage) : 'https://placehold.co/400x400/FDFBF9/B4965A?text=No+Image' }}"
                             class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700 grayscale-[0.3] group-hover:grayscale-0">

                        <div class="absolute bottom-2 left-2">
                            <span class="px-2 py-1 bg-white/90 backdrop-blur text-[8px] font-bold uppercase tracking-widest rounded-lg">Stock: {{ $product->stock }}</span>
                        </div>
                    </div>

                    <h4 class="font-serif text-sm text-luxury-dark truncate">{{ $product->name }}</h4>
                    <p class="text-luxury-gold font-bold text-xs mt-1 font-mono tracking-tighter">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </button>
            @endforeach
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4 bg-white shadow-2xl flex flex-col h-screen sticky top-0">
        <div class="p-8 border-b border-gray-50">
            <h3 class="font-serif text-2xl italic mb-6">Current Order</h3>

            <div class="space-y-4">
                <input type="text" wire:model="customer_name" placeholder="Client Name" class="w-full border-gray-100 rounded-xl text-sm py-3 focus:ring-luxury-gold bg-gray-50/50">
                <div class="grid grid-cols-2 gap-3">
                    <select wire:model.live="type" class="border-gray-100 rounded-xl text-[10px] font-bold uppercase tracking-widest bg-gray-50/50">
                        <option value="offline">Offline / In-Store</option>
                        <option value="online">Online / Remote</option>
                    </select>
                    <select wire:model="payment_method" class="border-gray-100 rounded-xl text-[10px] font-bold uppercase tracking-widest bg-gray-50/50">
                        <option value="cash">Cash Payment</option>
                        <option value="transfer">Bank Transfer</option>
                        <option value="qris">QRIS / Digital</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex-grow overflow-y-auto p-8 space-y-6 custom-scrollbar">
            @forelse($cart as $id => $item)
                <div class="flex items-center gap-4 group">
                    <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden flex-shrink-0">
                        <img src="{{ $item['image'] ?: 'https://placehold.co/100x100?text=Item' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-grow">
                        <h5 class="text-xs font-bold text-luxury-dark uppercase tracking-tight">{{ $item['name'] }}</h5>
                        <p class="text-[10px] text-luxury-gold font-mono font-bold mt-1">Rp {{ number_format($item['price']) }}</p>
                    </div>
                    <div class="flex items-center bg-gray-50 rounded-lg p-1">
                        <button wire:click="decrementQty({{ $id }})" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-luxury-dark">-</button>
                        <span class="w-8 text-center text-xs font-bold">{{ $item['qty'] }}</span>
                        <button wire:click="incrementQty({{ $id }})" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-luxury-dark">+</button>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-center space-y-4 opacity-30">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="1"/></svg>
                    <p class="font-serif italic text-sm text-gray-400">The vault is currently empty...</p>
                </div>
            @endforelse
        </div>

        <div class="p-8 bg-gray-50/50 border-t border-gray-100 space-y-4">
            <div class="space-y-2">
                <div class="flex justify-between text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span>Subtotal</span>
                    <span class="font-mono">Rp {{ number_format($this->subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span>Discount</span>
                    <input type="number" wire:model.live="discount" class="w-24 text-right border-none bg-transparent p-0 text-xs font-mono text-red-400 focus:ring-0">
                </div>
                <div class="pt-4 border-t border-gray-200 flex justify-between items-end">
                    <span class="font-serif text-lg italic">Investment Total</span>
                    <span class="font-serif text-3xl text-luxury-gold">Rp {{ number_format($this->grand_total) }}</span>
                </div>
            </div>

            <button wire:click="submit"
                    {{ empty($cart) ? 'disabled' : '' }}
                    class="w-full bg-luxury-dark text-white py-5 rounded-[1.5rem] font-serif text-lg hover:bg-black transition-all shadow-luxury-gold/20 shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed group flex items-center justify-center gap-3">
                <svg class="w-5 h-5 text-luxury-gold group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2"/></svg>
                Complete Transaction
            </button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #D4AF37; }
</style>
