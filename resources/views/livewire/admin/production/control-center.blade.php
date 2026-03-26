<?php

use Livewire\Volt\Component;
use App\Models\{ProductionBatch, Product, ProductionCost, InventoryLog};
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithPagination;

    public $showCreateModal = false;
    public $openFolders = ['pending', 'printing']; // Folder yang terbuka default

    // State Form
    public $product_id, $target_quantity, $start_date, $notes;
    public $costs = [];

    public $stages = [
        'pending'   => ['label' => 'Antrian Produksi', 'icon' => 'layers', 'color' => 'gray'],
        'printing'  => ['label' => 'Proses Printing', 'icon' => 'printer', 'color' => 'blue'],
        'cutting'   => ['label' => 'Proses Cutting', 'icon' => 'scissors', 'color' => 'orange'],
        'finishing' => ['label' => 'Finishing & QC', 'icon' => 'check-circle', 'color' => 'purple'],
    ];

    public function mount() {
        $this->start_date = now()->format('Y-m-d');
        $this->resetCostRows();
    }

    public function toggleFolder($stage) {
        if (in_array($stage, $this->openFolders)) {
            $this->openFolders = array_diff($this->openFolders, [$stage]);
        } else {
            $this->openFolders[] = $stage;
        }
    }

    public function getProgress($status) {
        $keys = array_keys($this->stages);
        $index = array_search($status, $keys);
        return (($index + 1) / count($keys)) * 100;
    }

    public function updateStatus($id, $newStatus) {
        ProductionBatch::find($id)->update(['status' => $newStatus]);
        session()->flash('message', 'Status diperbarui.');
    }

    public function resetCostRows() {
        $this->costs = [['category' => 'Bahan Baku', 'item' => '', 'amount' => 0]];
    }

    public function addCostRow() { $this->costs[] = ['category' => 'Lainnya', 'item' => '', 'amount' => 0]; }

    public function saveProduction() {
        $this->validate(['product_id' => 'required', 'target_quantity' => 'required|numeric']);

        DB::transaction(function () {
            $batch = ProductionBatch::create([
                'product_id' => $this->product_id,
                'batch_code' => 'BATCH-' . now()->format('is'),
                'target_quantity' => $this->target_quantity,
                'start_date' => $this->start_date,
                'status' => 'pending'
            ]);
            foreach ($this->costs as $c) {
                if($c['amount'] > 0) $batch->costs()->create(['category' => $c['category'], 'item_name' => $c['item'], 'amount' => $c['amount']]);
            }
        });
        $this->reset(['showCreateModal', 'product_id', 'target_quantity']);
    }

    public function completeProduction($id, $finalQty) {
        if(!$finalQty) return;
        DB::transaction(function () use ($id, $finalQty) {
            $batch = ProductionBatch::findOrFail($id);
            $batch->update(['status' => 'completed', 'actual_quantity' => $finalQty, 'end_date' => now()]);
            $batch->product->increment('stock', $finalQty);
            InventoryLog::create([
                'product_id' => $batch->product_id, 'user_id' => auth()->id(), 'type' => 'in',
                'quantity' => $finalQty, 'batch_id' => $batch->id, 'reason' => "Produksi Selesai: {$batch->batch_code}"
            ]);
        });
    }

    public function with() {
        // Pagination per status menggunakan manual collection atau multiple query
        // Untuk data sangat banyak, kita query per folder
        $data = [];
        foreach($this->stages as $key => $val) {
            $data[$key] = ProductionBatch::with(['product', 'costs'])
                ->where('status', $key)
                ->latest()
                ->paginate(5, ['*'], $key.'Page');
        }

        return [
            'batches' => $data,
            'products' => Product::all()
        ];
    }
}; ?>

<div class="space-y-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-end mb-8">
        <div class="space-y-1">
            <h2 class="font-serif text-4xl italic text-luxury-dark">Production <span class="text-luxury-gold">Hub</span></h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.4em]">Organisasi Batch Skala Besar</p>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="bg-luxury-dark text-white px-10 py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-black transition-all shadow-xl">
            + Batch Baru
        </button>
    </div>

    <div class="space-y-4">
        @foreach($stages as $key => $stage)
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden transition-all">
            <button wire:click="toggleFolder('{{ $key }}')"
                    class="w-full flex items-center justify-between p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-luxury-dark flex items-center justify-center text-luxury-gold shadow-lg">
                        @if($key == 'pending') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                        @elseif($key == 'printing') <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2"/></svg>
                        @else <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/></svg>
                        @endif
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-black text-luxury-dark uppercase tracking-widest">{{ $stage['label'] }}</h4>
                        <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $batches[$key]->total() }} Batch Terdaftar</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-mono font-bold text-gray-300">FOLDER ID: {{ strtoupper($key) }}</span>
                    <svg class="w-5 h-5 text-gray-300 transition-transform {{ in_array($key, $openFolders) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                </div>
            </button>

            @if(in_array($key, $openFolders))
            <div class="p-6 bg-[#FDFBF9] border-t border-gray-50 animate-in slide-in-from-top duration-300">
                <div class="grid grid-cols-1 gap-4">
                    @forelse($batches[$key] as $item)
                    <div class="bg-white p-6 rounded-[1.5rem] border border-gray-100 flex flex-wrap md:flex-nowrap items-center justify-between gap-6 hover:shadow-md transition-shadow">
                        <div class="w-full md:w-1/4">
                            <span class="text-[8px] font-mono text-gray-400 block mb-1">#{{ $item->batch_code }}</span>
                            <h5 class="text-sm font-black text-luxury-dark uppercase tracking-tight">{{ $item->product->name }}</h5>
                            <div class="flex gap-2 mt-2">
                                <span class="text-[9px] bg-gray-50 px-2 py-0.5 rounded-md text-gray-500 font-bold">Target: {{ $item->target_quantity }}</span>
                            </div>
                        </div>

                        <div class="w-full md:w-1/3">
                            <div class="flex justify-between mb-1 text-[8px] font-bold uppercase text-gray-400">
                                <span>Progres Keseluruhan</span>
                                <span class="text-luxury-gold">{{ round($this->getProgress($item->status)) }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-luxury-gold transition-all duration-700" style="width: {{ $this->getProgress($item->status) }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center gap-8">
                            <div class="text-right">
                                <p class="text-[8px] font-bold text-gray-300 uppercase leading-none">Total Cost</p>
                                <p class="text-xs font-mono font-bold text-luxury-dark">Rp {{ number_format($item->total_cost) }}</p>
                            </div>

                            @if($key !== 'finishing')
                            <button wire:click="updateStatus({{ $item->id }}, '{{ array_keys($stages)[array_search($key, array_keys($stages)) + 1] }}')"
                                    class="bg-luxury-dark text-white px-4 py-2 rounded-xl text-[9px] font-bold uppercase tracking-widest hover:bg-luxury-gold transition-all">
                                Lanjut Ke {{ $stages[array_keys($stages)[array_search($key, array_keys($stages)) + 1]]['label'] }} →
                            </button>
                            @else
                            <div class="flex gap-2">
                                <input type="number" id="fin_{{ $item->id }}" placeholder="Qty Jadi" class="w-20 bg-gray-50 border-none rounded-lg text-[10px] py-2 px-3 shadow-inner">
                                <button wire:click="completeProduction({{ $item->id }}, document.getElementById('fin_{{ $item->id }}').value)"
                                        class="bg-emerald-500 text-white px-4 py-2 rounded-xl text-[9px] font-bold uppercase tracking-widest shadow-lg">
                                    Simpan & Masuk Stok
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 italic text-[10px] text-gray-400 uppercase tracking-widest">Tidak ada batch di folder ini.</div>
                    @endforelse

                    <div class="mt-4">
                        {{ $batches[$key]->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    @if($showCreateModal)
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
            <div class="grid grid-cols-12">
                <div class="col-span-12 md:col-span-7 p-12 space-y-6">
                    <h2 class="font-serif text-3xl italic">Inisiasi <span class="text-luxury-gold">Batch</span></h2>
                    <div class="space-y-4">
                        <select wire:model="product_id" class="w-full bg-gray-50 border-none rounded-2xl py-4 text-sm focus:ring-luxury-gold">
                            <option value="">Pilih Produk...</option>
                            @foreach($products as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                        </select>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="number" wire:model="target_quantity" placeholder="Target Qty" class="w-full bg-gray-50 border-none rounded-2xl py-4 text-sm focus:ring-luxury-gold">
                            <input type="date" wire:model="start_date" class="w-full bg-gray-50 border-none rounded-2xl py-4 text-sm focus:ring-luxury-gold">
                        </div>
                    </div>
                </div>
                <div class="col-span-12 md:col-span-5 bg-[#FDFBF9] p-12 border-l border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <p class="text-[10px] font-bold uppercase tracking-widest">Alokasi Dana</p>
                        <button wire:click="addCostRow" class="text-[10px] font-bold text-luxury-gold">+ TAMBAH</button>
                    </div>
                    <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                        @foreach($costs as $index => $c)
                        <div class="flex gap-2">
                            <input type="text" wire:model="costs.{{$index}}.item" placeholder="Item" class="w-3/5 bg-white border-none rounded-xl py-2 text-[10px] shadow-sm">
                            <input type="number" wire:model="costs.{{$index}}.amount" placeholder="Rp" class="w-2/5 bg-white border-none rounded-xl py-2 text-[10px] shadow-sm">
                        </div>
                        @endforeach
                    </div>
                    <button wire:click="saveProduction" class="w-full mt-10 bg-luxury-dark text-white py-4 rounded-2xl text-[10px] font-bold uppercase tracking-widest">Buka Batch Produksi</button>
                    <button wire:click="$set('showCreateModal', false)" class="w-full mt-4 text-[9px] font-bold text-gray-400 uppercase">Batalkan</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
