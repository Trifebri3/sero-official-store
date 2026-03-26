<?php

use Livewire\Volt\Component;
use App\Models\{InventoryLog, InventoryBatch, Product};
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    // Filter States
    public $search = '';
    public $filterType = '';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    // Mengatur Reset Halaman saat mencari
    public function updatedSearch() { $this->resetPage(); }

    // Logic: Mengambil Data Log
    public function getLogs()
    {
        return InventoryLog::with(['product', 'user', 'batch'])
            ->when($this->search, function($query) {
                $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                      ->orWhere('reason', 'like', "%{$this->search}%")
                      ->orWhere('type', 'like', "%{$this->search}%");
            })
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->latest()
            ->paginate(10); // Dikurangi agar tabel tidak terlalu panjang
    }

    // Logic: Menghitung Statistik Ringkas
    public function getStats()
    {
        $logs = InventoryLog::with('batch')
            ->whereIn('type', ['out', 'production_waste'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        return [
            'potential_loss' => $logs->sum(fn($log) => $log->quantity * ($log->batch->production_cost_per_unit ?? 0)),
            'total_events' => $logs->count(),
            'top_waste' => InventoryLog::where('type', 'out')
                ->selectRaw('product_id, sum(quantity) as total')
                ->groupBy('product_id')->with('product')->orderByDesc('total')->take(3)->get()
        ];
    }

    public function with()
    {
        return [
            'logs' => $this->getLogs(),
            'analysis' => $this->getStats(),
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:w-1/3">
                <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama barang atau catatan..."
                    class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-luxury-gold transition-all">
            </div>

            <div class="flex flex-wrap md:flex-nowrap gap-3 w-full md:w-2/3 justify-end">
                <select wire:model.live="filterType" class="py-3 px-4 bg-gray-50 border-none rounded-2xl text-xs font-bold uppercase tracking-wider text-gray-500 focus:ring-2 focus:ring-luxury-gold">
                    <option value="">Semua Aktivitas</option>
                    <option value="in">Barang Masuk</option>
                    <option value="out">Keluar/Limbah</option>
                    <option value="adjustment">Audit Stok</option>
                </select>

                <div class="flex items-center bg-gray-50 rounded-2xl px-3 border-none">
                    <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-xs focus:ring-0 py-3">
                    <span class="text-gray-300 mx-1">-</span>
                    <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-xs focus:ring-0 py-3">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estimasi Rugi Operasional</p>
                <h4 class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($analysis['potential_loss'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <div class="md:col-span-2 bg-luxury-dark p-6 rounded-3xl text-white flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <p class="text-[10px] font-bold opacity-60 uppercase tracking-widest">3 Produk Terbuang Terbanyak</p>
                <div class="flex gap-3 mt-3">
                    @foreach($analysis['top_waste'] as $w)
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-2 rounded-xl border border-white/5">
                        <span class="text-luxury-gold font-bold text-sm">{{ $w->total }}</span>
                        <span class="text-[10px] uppercase tracking-tighter truncate w-20">{{ $w->product->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <button class="bg-luxury-gold px-6 py-3 rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] hover:scale-105 transition-transform">
                Unduh Laporan
            </button>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/80 border-b border-gray-100">
                    <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-5">Produk & Batch</th>
                        <th class="px-6 py-5">Aksi</th>
                        <th class="px-6 py-5">Qty</th>
                        <th class="px-6 py-5">Catatan Operasional</th>
                        <th class="px-6 py-5">User</th>
                        <th class="px-6 py-5 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-luxury-gold transition-colors">{{ $log->product->name }}</span>
                                <span class="text-[10px] font-mono text-gray-400 mt-1">#{{ $log->batch->batch_code ?? 'Manual_Adj' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase
                                {{ $log->type == 'in' ? 'bg-emerald-50 text-emerald-600' : ($log->type == 'out' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                                {{ $log->type == 'in' ? 'Masuk' : ($log->type == 'out' ? 'Limbah' : 'Audit') }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="font-mono font-bold {{ $log->type == 'in' ? 'text-emerald-500' : 'text-red-400' }}">
                                    {{ $log->type == 'in' ? '+' : '-' }}{{ $log->quantity }}
                                </span>
                                @if(isset($log->metadata['total_investment']))
                                <span class="text-[9px] text-gray-300 mt-1 italic">Rp {{ number_format($log->metadata['total_investment'], 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs text-gray-500 max-w-xs truncate lg:whitespace-normal italic leading-relaxed">"{{ $log->reason }}"</p>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-luxury-dark text-white flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </div>
                                <span class="text-[10px] font-bold text-gray-600 uppercase">{{ explode(' ', $log->user->name)[0] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <span class="block text-xs font-bold text-gray-600">{{ $log->created_at->translatedFormat('d M Y') }}</span>
                            <span class="text-[10px] text-gray-400">{{ $log->created_at->format('H:i') }} WIB</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-20">
                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="1"/></svg>
                                <p class="text-lg font-serif italic">Belum ada aktivitas terekam...</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
