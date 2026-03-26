<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use App\Models\BusinessSetting;
use Livewire\Attributes\Computed;

new class extends Component {

    #[Computed]
    public function stats()
    {
        // Ambil Blueprint Bisnis Terbaru
        $blueprint = BusinessSetting::where('key', 'default_goal')->first();
        $target = $blueprint ? (float) $blueprint->target_amount : 1; // Avoid division by zero

        // Hitung Omzet Bulan Ini
        $current_revenue = Transaction::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('grand_total');

        // Hitung Progress Omzet
        $progress_pct = ($current_revenue / $target) * 100;

        return [
            'daily' => Transaction::whereDate('created_at', today())->sum('grand_total'),
            'monthly' => $current_revenue,
            'target' => $target,
            'progress' => round($progress_pct, 1),
            'online_count' => Transaction::where('type', 'online')->count(),
            'offline_count' => Transaction::where('type', 'offline')->count(),
            'blueprint' => $blueprint
        ];
    }

    #[Computed]
    public function recentLogs()
    {
        return Transaction::latest()->take(6)->get();
    }
}; ?>

<div class="p-8 space-y-8 bg-[#F9FAFB] min-h-screen">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="font-serif text-3xl italic text-luxury-dark">Terminal POS <span class="text-luxury-gold">& Digital Vault</span></h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.4em] mt-1">Unified Command Center - Ranaloka Creative</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.transactions.pos') }}" class="bg-luxury-dark text-white px-6 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-black transition-all">Open Terminal</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm relative overflow-hidden group">
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Today's Pulse</p>
            <h2 class="text-3xl font-serif mt-2 text-luxury-dark">Rp {{ number_format($this->stats['daily']) }}</h2>
            <div class="mt-4 flex items-center text-[10px] font-bold text-green-500 uppercase">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke-width="3"/></svg>
                Real-time Sync Active
            </div>
        </div>

        <div class="col-span-1 md:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Revenue Goal Progress</p>
                    <h2 class="text-3xl font-serif mt-2 text-luxury-dark">
                        {{ $this->stats['progress'] }}% <span class="text-xs text-gray-400">Achieved</span>
                    </h2>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-luxury-gold uppercase tracking-widest">Target</p>
                    <p class="font-serif italic text-sm">Rp {{ number_format($this->stats['target']) }}</p>
                </div>
            </div>
            <div class="mt-6 w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                <div class="bg-luxury-gold h-full transition-all duration-1000 shadow-[0_0_15px_rgba(212,175,55,0.5)]" style="width: {{ $this->stats['progress'] }}%"></div>
            </div>
            <p class="text-[10px] mt-4 text-gray-400 italic font-medium uppercase tracking-widest">Current Month: Rp {{ number_format($this->stats['monthly']) }}</p>
        </div>

        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Channel Integrity</p>
            <div class="space-y-4 mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold uppercase text-blue-500">Digital (Online)</span>
                    <span class="font-mono font-bold">{{ $this->stats['online_count'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-bold uppercase text-green-500">Physical (Offline)</span>
                    <span class="font-mono font-bold">{{ $this->stats['offline_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 lg:col-span-8 bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-serif text-xl italic text-luxury-dark tracking-wide">Digital Vault Logs</h3>
                <a href="{{ route('admin.transactions.history') }}" class="text-[10px] font-bold text-luxury-gold tracking-[0.2em] hover:underline">VIEW ARCHIVE</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-[9px] uppercase tracking-[0.2em] font-bold text-gray-400">
                        <tr>
                            <th class="px-8 py-5">Trace ID</th>
                            <th class="px-8 py-5 text-center">Protocol</th>
                            <th class="px-8 py-5 text-right">Value (IDR)</th>
                            <th class="px-8 py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($this->recentLogs as $log)
                            <tr class="hover:bg-gray-50/80 transition-all group">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-luxury-dark tracking-tight">{{ $log->reference_no }}</span>
                                        <span class="text-[9px] text-gray-400 font-mono">{{ $log->created_at->format('d M, H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase {{ $log->type == 'online' ? 'bg-blue-50 text-blue-600' : 'bg-green-50 text-green-600' }}">
                                        {{ $log->type }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right font-serif italic text-luxury-dark">
                                    {{ number_format($log->grand_total) }}
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block ring-4 ring-green-50"></span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-luxury-dark p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden">
                <h3 class="font-serif text-xl italic mb-6">Social Impact <span class="text-luxury-gold">Allocation</span></h3>
                <div class="space-y-6 relative z-10">
                    @if($this->stats['blueprint'])
                        @foreach($this->stats['blueprint']->allocations as $alloc)
                            <div>
                                <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest mb-2">
                                    <span class="text-gray-400">{{ $alloc['name'] }}</span>
                                    <span class="text-luxury-gold">{{ $alloc['pct'] }}%</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <p class="font-serif text-lg">Rp {{ number_format(($this->stats['monthly'] * (float)$alloc['pct']) / 100) }}</p>
                                </div>
                                <div class="w-full bg-white/5 h-1 mt-2 rounded-full">
                                    <div class="bg-white/20 h-full rounded-full" style="width: {{ $alloc['pct'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-400 italic">No business blueprint found. Set your Revenue Goal first.</p>
                    @endif
                </div>
                <svg class="absolute -right-10 -bottom-10 w-48 h-48 opacity-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="font-serif text-lg mb-4 italic">Quick Analysis</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.financial.goal') }}" class="p-4 bg-gray-50 rounded-2xl text-center group hover:bg-luxury-gold transition-all">
                        <p class="text-[9px] font-bold text-gray-400 uppercase group-hover:text-white">Blueprints</p>
                    </a>
                    <a href="{{ route('admin.transactions.history') }}" class="p-4 bg-gray-50 rounded-2xl text-center group hover:bg-luxury-dark transition-all">
                        <p class="text-[9px] font-bold text-gray-400 uppercase group-hover:text-white">Archives</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
