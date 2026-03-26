<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\InventoryBatch;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $activeTab = 'monitor';

    // State Form Pengadaan/Restock
    public $selectedProduct;
    public $qty;
    public $costPerUnit;
    public $batchCode;
    public $notes;
    public $cost_breakdown = [
        'raw_material' => 0,
        'labor' => 0,
        'shipping' => 0,
        'other' => 0
    ];

    // State Form Penyesuaian Stok (Rusak/Hilang)
    public $adjProduct;
    public $adjQty;
    public $adjType = 'out';
    public $adjReason;

    public function processRestock()
    {
        $this->validate([
            'selectedProduct' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:1',
            'costPerUnit' => 'required|numeric|min:0',
            'batchCode' => 'required|unique:inventory_batches,batch_code'
        ], [
            'batchCode.unique' => 'Kode Batch ini sudah terdaftar.',
            'selectedProduct.required' => 'Pilih produk terlebih dahulu.'
        ]);

        DB::transaction(function () {
            $batch = InventoryBatch::create([
                'product_id' => $this->selectedProduct,
                'batch_code' => $this->batchCode,
                'initial_stock' => $this->qty,
                'current_stock' => $this->qty,
                'production_cost_per_unit' => $this->costPerUnit,
                'cost_breakdown' => $this->cost_breakdown,
                'started_at' => now(),
                'status' => 'active'
            ]);

            Product::find($this->selectedProduct)->increment('stock', $this->qty);

            InventoryLog::create([
                'product_id' => $this->selectedProduct,
                'batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $this->qty,
                'reason' => "Pengadaan Baru: Batch {$this->batchCode}",
                'metadata' => [
                    'notes' => $this->notes,
                    'total_investment' => $this->qty * $this->costPerUnit
                ],
                'user_id' => auth()->id()
            ]);
        });

        $this->reset(['selectedProduct', 'qty', 'costPerUnit', 'batchCode', 'notes', 'cost_breakdown']);
        $this->activeTab = 'monitor';
        session()->flash('message', 'Periode Produksi Baru Berhasil Dimulai.');
    }

    public function processAdjustment()
    {
        $this->validate([
            'adjProduct' => 'required',
            'adjQty' => 'required|numeric|min:1',
            'adjReason' => 'required'
        ]);

        DB::transaction(function () {
            $product = Product::find($this->adjProduct);
            $product->decrement('stock', $this->adjQty);

            InventoryLog::create([
                'product_id' => $this->adjProduct,
                'type' => $this->adjType,
                'quantity' => $this->adjQty,
                'reason' => "Penyesuaian Manual: " . $this->adjReason,
                'metadata' => ['manual' => true],
                'user_id' => auth()->id()
            ]);
        });

        $this->reset(['adjProduct', 'adjQty', 'adjReason']);
        session()->flash('message', 'Stok Berhasil Disesuaikan.');
    }

    public function with()
    {
        return [
            'activeBatches' => InventoryBatch::with('product')
                ->where('status', 'active')
                ->orderBy('started_at', 'desc')
                ->get(),
            'products' => Product::select('id', 'name', 'stock')->get(),
            'recentLogs' => InventoryLog::with(['product', 'user'])
                ->latest()
                ->take(15)
                ->get(),
            'inventoryStats' => [
                'total_value' => InventoryBatch::where('status', 'active')
                    ->selectRaw('SUM(current_stock * production_cost_per_unit) as value')
                    ->first()->value ?? 0,
                'low_stock_count' => Product::where('stock', '<', 10)->count()
            ]
        ];
    }
}; ?>

<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-luxury-dark p-6 rounded-[2rem] text-white shadow-2xl">
            <p class="text-[10px] uppercase tracking-[0.2em] font-bold opacity-60">Total Nilai Aset</p>
            <h3 class="font-serif text-2xl italic mt-2">Rp {{ number_format($inventoryStats['total_value']) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-gray-400">Batch Aktif</p>
            <h3 class="font-serif text-2xl italic mt-2 text-luxury-dark">{{ $activeBatches->count() }} Periode</h3>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-gray-400">Peringatan Stok</p>
            <h3 class="font-serif text-2xl italic mt-2 text-red-500">{{ $inventoryStats['low_stock_count'] }} Produk Menipis</h3>
        </div>
        <div class="bg-luxury-gold p-6 rounded-[2rem] text-white shadow-lg">
            <p class="text-[10px] uppercase tracking-[0.2em] font-bold opacity-80">Pergerakan Hari Ini</p>
            <h3 class="font-serif text-2xl italic mt-2">{{ $recentLogs->where('created_at', '>=', today())->count() }} Aktivitas</h3>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8">

        <div class="col-span-12 lg:col-span-8 space-y-8">

            <div class="flex gap-4 border-b border-gray-100 pb-2">
                <button wire:click="$set('activeTab', 'monitor')" class="pb-2 px-4 text-xs font-bold uppercase tracking-widest transition-all {{ $activeTab == 'monitor' ? 'text-luxury-gold border-b-2 border-luxury-gold' : 'text-gray-300' }}">Monitoring Aktif</button>
                <button wire:click="$set('activeTab', 'logs')" class="pb-2 px-4 text-xs font-bold uppercase tracking-widest transition-all {{ $activeTab == 'logs' ? 'text-luxury-gold border-b-2 border-luxury-gold' : 'text-gray-300' }}">Riwayat Audit</button>
            </div>

            @if($activeTab == 'monitor')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($activeBatches as $batch)
                        <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-6">
                                <div class="p-3 bg-[#FDFBF9] rounded-2xl group-hover:bg-luxury-gold/10 transition-colors">
                                    <svg class="w-5 h-5 text-luxury-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2"/></svg>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] font-mono text-gray-400">ID: {{ $batch->batch_code }}</span>
                                    <span class="text-[8px] font-bold text-luxury-gold uppercase tracking-tighter">{{ $batch->started_at->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>

                            <h4 class="font-serif text-xl text-luxury-dark mb-1">{{ $batch->product->name }}</h4>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">Biaya Per Unit: Rp {{ number_format($batch->production_cost_per_unit) }}</p>

                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest">
                                    <span class="text-gray-400">Level Inventaris</span>
                                    <span class="{{ $batch->current_stock < 10 ? 'text-red-500' : 'text-luxury-dark' }}">
                                        {{ $batch->current_stock }} / {{ $batch->initial_stock }} Sisa
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-luxury-gold h-full transition-all duration-1000" style="width: {{ ($batch->current_stock / $batch->initial_stock) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-[2rem] border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Produk</th>
                                <th class="px-6 py-4">Kejadian</th>
                                <th class="px-6 py-4">Jumlah</th>
                                <th class="px-6 py-4">Petugas</th>
                                <th class="px-6 py-4 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentLogs as $log)
                                <tr class="text-xs hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold">{{ $log->product->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded-md {{ $log->type == 'in' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }} text-[9px] font-bold uppercase">
                                            {{ $log->type == 'in' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono">{{ $log->quantity }}</td>
                                    <td class="px-6 py-4 text-gray-400">{{ $log->user->name }}</td>
                                    <td class="px-6 py-4 text-right text-gray-300">{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="col-span-12 lg:col-span-4 space-y-8">

            <div class="bg-[#FDFBF9] p-8 rounded-[3rem] border border-gray-100 shadow-sm">
                <h3 class="font-serif text-xl italic mb-6">Protokol Periode Baru</h3>
                <form wire:submit.prevent="processRestock" class="space-y-4">
                    <div>
                        <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-2">Pilih Masterpiece</label>
                        <select wire:model="selectedProduct" class="w-full mt-1 bg-white border-none rounded-2xl py-3 text-xs shadow-sm focus:ring-luxury-gold">
                            <option value="">Pilih Produk...</option>
                            @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->stock }} sisa)</option> @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-2">Jumlah</label>
                            <input type="number" wire:model="qty" class="w-full mt-1 bg-white border-none rounded-2xl py-3 text-xs shadow-sm focus:ring-luxury-gold">
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-2">HPP Rata-rata</label>
                            <input type="number" wire:model="costPerUnit" class="w-full mt-1 bg-white border-none rounded-2xl py-3 text-xs shadow-sm focus:ring-luxury-gold">
                        </div>
                    </div>

                    <div>
                        <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-2">Identitas Batch</label>
                        <input type="text" wire:model="batchCode" placeholder="Contoh: BATCH-2026-A" class="w-full mt-1 bg-white border-none rounded-2xl py-3 text-xs shadow-sm focus:ring-luxury-gold font-mono uppercase">
                    </div>

                    <button type="submit" class="w-full bg-luxury-dark text-white py-4 rounded-2xl font-serif hover:bg-black transition-all shadow-lg mt-4">
                        Inisialisasi Periode
                    </button>
                </form>
            </div>

            <div class="bg-white p-8 rounded-[3rem] border border-red-50 shadow-sm">
                <h3 class="font-serif text-xl italic mb-6 text-red-900">Penyesuaian Inventaris</h3>
                <form wire:submit.prevent="processAdjustment" class="space-y-4">
                    <select wire:model="adjProduct" class="w-full bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-red-400">
                        <option value="">Pilih Produk...</option>
                        @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                    </select>

                    <div class="grid grid-cols-2 gap-4">
                        <input type="number" wire:model="adjQty" placeholder="Jumlah" class="bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-red-400">
                        <select wire:model="adjType" class="bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-red-400">
                            <option value="out">Rusak/Limbah</option>
                            <option value="adjustment">Audit Stok</option>
                        </select>
                    </div>

                    <input type="text" wire:model="adjReason" placeholder="Alasan penyesuaian..." class="w-full bg-gray-50 border-none rounded-2xl py-3 text-xs focus:ring-red-400">

                    <button type="submit" class="w-full border border-red-100 text-red-500 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                        Rekam Penyesuaian
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
