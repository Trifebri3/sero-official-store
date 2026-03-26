<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <span class="text-[10px] font-black text-luxury-gold uppercase tracking-[0.4em] mb-1">Operational Overview</span>
            <h2 class="font-serif italic text-3xl text-luxury-dark leading-tight">
                {{ __('SERO Executive Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:admin.dashboard-stats />
        </div>
    </div>
</x-app-layout>
