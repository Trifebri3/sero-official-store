<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl text-luxury-dark leading-tight">
            Inventory & Collection Hub
        </h2>
    </x-slot>

    <div class="py-12 bg-luxury-cream min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:admin.product-index />
        </div>
    </div>
</x-app-layout>
