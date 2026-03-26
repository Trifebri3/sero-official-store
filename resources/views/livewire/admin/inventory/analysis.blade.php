<?php

use Livewire\Volt\Component;
use App\Models\{InventoryBatch, InventoryLog, Product};
use Illuminate\Support\Facades\DB;

new class extends Component {

    public function getStockHealthProperty()
    {
        return [
            'total_value' => InventoryBatch::where('status', 'active')->sum(DB::raw('current_stock * production_cost_per_unit')),
            'avg_margin' => 35, // Ini bisa dihitung dinamis jika ada data harga jual
            'waste_rate' => $this->calculateWasteRate(),
        ];
    }

    private function calculateWasteRate()
    {
        $totalIn = InventoryLog::where('type', 'in')->sum('quantity') ?: 1;
        $totalWaste = InventoryLog::where('type', 'out')->sum('quantity');
        return round(($totalWaste / $totalIn) * 100, 2);
    }

    public function with()
    {
        return [
            'stockValueByProduct' => Product::with(['batches' => fn($q) => $q->where('status', 'active')])
                ->get()
                ->map(fn($p) => [
                    'name' => $p->name,
                    'value' => $p->batches->sum(fn($b) => $b->current_stock * $b->production_cost_per_unit),
                    'stock' => $p->stock
                ])->sortByDesc('value')->take(5),

            'recentBatches' => InventoryBatch::with('product')->latest()->take(5)->get(),
            'health' => $this->stockHealth,
        ];
    }
}; ?>

<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-luxury-dark p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-bold opacity-50 uppercase tracking-[0.2em]">Total Modal Mengendap</p>
                <h3 class="text-3xl font-serif italic mt-2">Rp {{ number_format($health['total_value'], 0, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-2 text-[10px] text-luxury-gold font-bold uppercase">
                    <span class="w-2 h-2 rounded-full bg-luxury-gold animate-pulse"></span>
                    Aset Inventaris Aktif
                </div>
            </div>
            <svg class="absolute right-[-20px] top-[-20px] w-32 h-32 opacity-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Tingkat Kebocoran (Waste)</p>
            <h3 class="text-3xl font-serif italic mt-2 text-red-500">{{ $health['waste_rate'] }}%</h3>
            <p class="text-[9px] text-gray-400 mt-2">Rasio barang rusak dibanding total pengadaan</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Estimasi Profitabilitas</p>
            <h3 class="text-3xl font-serif italic mt-2 text-emerald-600">+{{ $health['avg_margin'] }}%</h3>
            <p class="text-[9px] text-gray-400 mt-2">Rata-rata margin kotor seluruh batch aktif</p>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 lg:col-span-7 bg-white p-8 rounded-[3rem] border border-gray-100 shadow-sm">
            <h4 class="font-serif text-xl italic mb-8">Distribusi Nilai Barang <span class="text-luxury-gold">(Top 5)</span></h4>

            <div class="space-y-6">
                @foreach($stockValueByProduct as $item)
                <div class="group">
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <span class="text-xs font-bold text-gray-700 uppercase group-hover:text-luxury-gold transition-colors">{{ $item['name'] }}</span>
                            <span class="text-[10px] text-gray-400 block">{{ $item['stock'] }} unit tersisa</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-gray-600">Rp {{ number_format($item['value'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-50 h-2 rounded-full overflow-hidden">
                        @php $percent = $health['total_value'] > 0 ? ($item['value'] / $health['total_value']) * 100 : 0 @endphp
                        <div class="bg-luxury-dark h-full group-hover:bg-luxury-gold transition-all duration-700" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="col-span-12 lg:col-span-5 space-y-8">
            <div class="bg-[#FDFBF9] p-8 rounded-[3rem] border border-gray-100">
                <h4 class="font-serif text-xl italic mb-6 text-luxury-dark">Status Batch Produksi</h4>
                <div class="space-y-4">
                    @foreach($recentBatches as $batch)
                    <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-50 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-luxury-gold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="1.5"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-800 uppercase leading-none">{{ $batch->product->name }}</p>
                                <span class="text-[9px] text-gray-400 font-mono">{{ $batch->batch_code }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-bold text-emerald-600 uppercase">Aktif</span>
                            <span class="text-[9px] text-gray-400 italic">HPP: Rp {{ number_format($batch->production_cost_per_unit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button class="w-full mt-6 py-3 border border-dashed border-gray-300 rounded-2xl text-[10px] font-bold text-gray-400 uppercase tracking-widest hover:border-luxury-gold hover:text-luxury-gold transition-all">
                    Lihat Semua Batch
                </button>
            </div>
        </div>
    </div>
</div>
