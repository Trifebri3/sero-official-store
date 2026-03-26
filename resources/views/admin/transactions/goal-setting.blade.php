<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif text-2xl text-luxury-dark leading-tight italic">
                Business Architect & Financial Goal
            </h2>
            <div class="text-[10px] font-bold text-luxury-gold uppercase tracking-[0.3em]">
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">
            <livewire:admin.transactions.goal-setting />
        </div>
    </div>
</x-app-layout>
