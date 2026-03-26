<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-xl text-luxury-dark leading-tight">
            Modify Luxury Item
        </h2>
    </x-slot>

    <div class="py-12 bg-luxury-cream">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:admin.product-edit :id="$id" />
        </div>
    </div>
</x-app-layout>
