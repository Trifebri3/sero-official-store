<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-luxury-dark leading-tight italic">
            Performance Analytics & Market Tracking
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:admin.transactions.dashboard />
        </div>
    </div>
</x-app-layout>
