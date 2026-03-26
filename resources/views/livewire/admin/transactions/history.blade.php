<?php

use Livewire\Volt\Component;
use App\Models\Transaction;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $filter_type = '';
    public $filter_status = '';

    public function updatingSearch() { $this->resetPage(); }

    public function with()
    {
        $query = Transaction::query()
            ->where('reference_no', 'like', "%{$this->search}%")
            ->orWhere('customer_info->name', 'like', "%{$this->search}%");

        if ($this->filter_type) $query->where('type', $this->filter_type);
        if ($this->filter_status) $query->where('status', $this->filter_status);

        return [
            'transactions' => $query->latest()->paginate(10)
        ];
    }
}; ?>

<div class="p-8 space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div class="space-y-1">
            <h2 class="font-serif text-2xl italic text-luxury-dark">Transaction Vault</h2>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tracking Every Masterpiece Movement</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <input type="text" wire:model.live="search" placeholder="Search Invoice / Customer..." class="text-xs border-gray-100 bg-gray-50 rounded-xl px-4 py-2 focus:ring-luxury-gold">

            <select wire:model.live="filter_type" class="text-xs border-gray-100 rounded-xl px-4 py-2">
                <option value="">All Channels</option>
                <option value="offline">In-Store (Offline)</option>
                <option value="online">Marketplace (Online)</option>
            </select>

            <button class="bg-luxury-gold/10 text-luxury-gold px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-luxury-gold/20 hover:bg-luxury-gold hover:text-white transition-all">
                Export CSV
            </button>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-luxury">
        <table class="w-full text-left">
            <thead class="bg-luxury-cream/30 text-[10px] uppercase font-bold text-luxury-dark tracking-[0.2em]">
                <tr>
                    <th class="p-6">Invoice & Date</th>
                    <th class="p-6">Channel & Logistics</th>
                    <th class="p-6">Acquirer (Customer)</th>
                    <th class="p-6 text-right">Investment Value</th>
                    <th class="p-6 text-center">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($transactions as $trx)
                    <tr class="hover:bg-luxury-cream/10 transition-colors group">
                        <td class="p-6">
                            <div class="font-mono text-xs font-bold text-luxury-dark">{{ $trx->reference_no }}</div>
                            <div class="text-[10px] text-gray-400 mt-1">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="p-6">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $trx->type == 'online' ? 'bg-blue-400' : 'bg-green-400' }}"></span>
                                <span class="text-[10px] font-bold uppercase tracking-widest">{{ $trx->channel ?? $trx->type }}</span>
                            </div>
                            <div class="text-[9px] text-gray-400 italic mt-1">Ref: {{ $trx->metadata['source'] ?? 'Direct' }}</div>
                        </td>
                        <td class="p-6">
                            <div class="text-xs font-bold text-luxury-dark uppercase">{{ $trx->customer_info['name'] ?? 'Guest' }}</div>
                            <div class="text-[10px] text-gray-400">{{ $trx->customer_info['phone'] ?? '-' }}</div>
                        </td>
                        <td class="p-6 text-right">
                            <div class="font-serif text-sm font-bold text-luxury-dark">Rp {{ number_format($trx->grand_total) }}</div>
                            <div class="text-[9px] text-luxury-gold uppercase font-bold italic">{{ count($trx->items) }} Items</div>
                        </td>
                        <td class="p-6">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.transactions.receipt', $trx->id) }}"
                                   class="p-2 bg-gray-50 text-gray-400 hover:text-luxury-gold rounded-xl border border-transparent hover:border-luxury-gold/30 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
