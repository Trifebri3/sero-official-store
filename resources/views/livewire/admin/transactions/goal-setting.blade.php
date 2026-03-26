<?php

use Livewire\Volt\Component;
use App\Models\BusinessSetting; // Pastikan buat modelnya dulu
use Livewire\Attributes\Computed;

new class extends Component {
    public $target_amount = 50000000;
    public $avg_price = 250000;
    public $avg_cost = 150000;

    // Daftar alokasi yang bisa ditambah/hapus sendiri
    public $custom_allocations = [
        ['name' => 'Operational', 'pct' => 30],
        ['name' => 'Human Resource', 'pct' => 20],
        ['name' => 'Social Impact', 'pct' => 10],
        ['name' => 'Net Profit', 'pct' => 40],
    ];

    public function mount() {
        $saved = BusinessSetting::where('key', 'default_goal')->first();
        if ($saved) {
            $this->target_amount = $saved->target_amount;
            $this->avg_price = $saved->avg_price;
            $this->avg_cost = $saved->avg_cost;
            $this->custom_allocations = $saved->allocations;
        }
    }

    public function addCategory() {
        $this->custom_allocations[] = ['name' => 'New Category', 'pct' => 0];
    }

    public function removeCategory($index) {
        unset($this->custom_allocations[$index]);
        $this->custom_allocations = array_values($this->custom_allocations);
    }

    public function saveSettings() {
        BusinessSetting::updateOrCreate(
            ['key' => 'default_goal'],
            [
                'target_amount' => $this->target_amount,
                'avg_price' => $this->avg_price,
                'avg_cost' => $this->avg_cost,
                'allocations' => $this->custom_allocations
            ]
        );
        session()->flash('message', 'Global Strategy Saved!');
    }

    #[Computed]
    public function results() {
        // Proteksi agar tidak error int * string
        $target = (float) $this->target_amount;
        $price = (float) $this->avg_price;
        $cost = (float) $this->avg_cost;

        $margin_unit = $price - $cost;
        $units = ($price > 0) ? ceil($target / $price) : 0;

        $calculated_allocs = [];
        foreach($this->custom_allocations as $alloc) {
            $calculated_allocs[] = [
                'name' => $alloc['name'],
                'pct' => $alloc['pct'],
                'nominal' => ($target * (float)$alloc['pct']) / 100
            ];
        }

        return [
            'units' => $units,
            'margin' => $margin_unit,
            'breakdown' => $calculated_allocs,
            'total_pct' => collect($this->custom_allocations)->sum('pct')
        ];
    }
}; ?>

<div class="p-8 max-w-7xl mx-auto space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <h1 class="font-serif text-3xl italic">Revenue Architect</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Customizable Financial Blueprint</p>
        </div>
        <button wire:click="saveSettings" class="bg-luxury-gold text-white px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:shadow-lg transition-all">
            Save Blueprint
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-green-50 text-green-600 rounded-2xl border border-green-100 text-xs italic">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 lg:col-span-5 space-y-6">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                <h3 class="font-serif text-xl mb-6 italic">Base Parameters</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase">Monthly Revenue Target</label>
                        <input type="number" wire:model.live="target_amount" class="w-full border-none bg-gray-50 rounded-xl mt-1 focus:ring-luxury-gold">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Avg Price</label>
                            <input type="number" wire:model.live="avg_price" class="w-full border-none bg-gray-50 rounded-xl mt-1 focus:ring-luxury-gold">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase">Avg Cost (HPP)</label>
                            <input type="number" wire:model.live="avg_cost" class="w-full border-none bg-gray-50 rounded-xl mt-1 focus:ring-luxury-gold">
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-between items-center mb-4">
                    <h3 class="font-serif text-xl italic">Margin Allocation</h3>
                    <button wire:click="addCategory" class="text-[10px] font-bold text-luxury-gold uppercase border border-luxury-gold px-3 py-1 rounded-lg">+ Add Item</button>
                </div>

                <div class="space-y-3">
                    @foreach($custom_allocations as $index => $alloc)
                        <div class="flex gap-2 items-center">
                            <input type="text" wire:model.live="custom_allocations.{{ $index }}.name" class="flex-1 border-none bg-gray-50 rounded-lg text-xs">
                            <input type="number" wire:model.live="custom_allocations.{{ $index }}.pct" class="w-20 border-none bg-gray-50 rounded-lg text-xs text-center">
                            <button wire:click="removeCategory({{ $index }})" class="text-red-300 hover:text-red-500">×</button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t flex justify-between">
                    <span class="text-[10px] font-bold uppercase">Total Allocation</span>
                    <span class="font-bold {{ $this->results['total_pct'] > 100 ? 'text-red-500' : 'text-luxury-gold' }}">
                        {{ $this->results['total_pct'] }}%
                    </span>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-7 space-y-6">
            <div class="bg-luxury-dark rounded-[2.5rem] p-10 text-white shadow-2xl">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.4em]">Sales Command</p>
                <div class="flex justify-between items-end mt-4">
                    <h2 class="font-serif text-6xl font-light">{{ number_format($this->results['units']) }} <span class="text-xl text-luxury-gold">Units/Month</span></h2>
                </div>
                <div class="mt-10 grid grid-cols-2 gap-8 border-t border-white/10 pt-8">
                    @foreach($this->results['breakdown'] as $item)
                        <div>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">{{ $item['name'] }} ({{ $item['pct'] }}%)</p>
                            <p class="font-serif text-xl mt-1 italic text-luxury-gold">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
