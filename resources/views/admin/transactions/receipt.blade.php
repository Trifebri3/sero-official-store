<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-xl text-luxury-dark leading-tight">
            Receipt Customizer
        </h2>
    </x-slot>

    <livewire:admin.transactions.receipt :id="$id" />
</x-app-layout>
