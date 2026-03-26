<?php

use Livewire\Volt\Component;
use App\Models\{Product, MarketingContent};

new class extends Component {
    public function with()
    {
        return [
            'totalProducts' => Product::count(),
            'totalContent' => MarketingContent::count(),
            'lowStock' => Product::where('stock', '<', 5)->count(),
            'recentCampaigns' => MarketingContent::with('product')->latest()->take(3)->get(),
        ];
    }
}; ?>

<div class="space-y-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-lg transition-all">
            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-4">Total Collection</p>
            <div class="flex items-end justify-between">
                <span class="font-serif text-4xl text-luxury-dark">{{ $totalProducts }}</span>
                <span class="text-[10px] font-bold text-luxury-gold italic">Masterpieces</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-lg transition-all">
            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-4">Marketing Assets</p>
            <div class="flex items-end justify-between">
                <span class="font-serif text-4xl text-luxury-dark">{{ $totalContent }}</span>
                <span class="text-[10px] font-bold text-luxury-gold italic">Campaigns</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-lg transition-all">
            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-4">Inventory Alert</p>
            <div class="flex items-end justify-between">
                <span class="font-serif text-4xl {{ $lowStock > 0 ? 'text-red-400' : 'text-luxury-dark' }}">{{ $lowStock }}</span>
                <span class="text-[10px] font-bold text-luxury-gold italic text-right leading-none">Critical Units</span>
            </div>
        </div>

        <div class="bg-luxury-dark p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-luxury-gold/10 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <p class="text-[9px] font-black text-luxury-gold/50 uppercase tracking-widest mb-4">Est. Valuation</p>
            <div class="flex items-end justify-between relative z-10">
                <span class="font-serif text-2xl text-white">IDR 12.5M</span>
                <span class="text-[10px] font-bold text-luxury-gold italic">Current Month</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[3rem] border border-gray-100 overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-50">
                        <tr>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Masterpiece</th>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Campaign</th>
                            <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentCampaigns as $campaign)
                        <tr class="group hover:bg-[#FDFBF9] transition-colors">
                            <td class="px-8 py-6">
                                <p class="text-xs font-bold text-luxury-dark">{{ $campaign->product->name }}</p>
                                <span class="text-[8px] font-mono text-gray-300">#{{ $campaign->product->sku }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-xs italic font-serif text-gray-600">{{ $campaign->title }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="text-[9px] font-black uppercase text-luxury-gold tracking-widest">{{ $campaign->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
             <a href="#" class="block p-6 bg-white border border-gray-100 rounded-[2rem] hover:border-luxury-gold transition-all">
                <p class="text-[10px] font-black uppercase tracking-widest text-luxury-dark">New Campaign</p>
                <p class="text-[10px] text-gray-400 italic font-serif">Create marketing assets</p>
             </a>
             <a href="#" class="block p-6 bg-luxury-dark border border-transparent rounded-[2rem] hover:shadow-luxury transition-all">
                <p class="text-[10px] font-black uppercase tracking-widest text-luxury-gold">Register Inventory</p>
                <p class="text-[10px] text-white/50 italic font-serif">Add new masterpiece</p>
             </a>
        </div>
    </div>
</div>
