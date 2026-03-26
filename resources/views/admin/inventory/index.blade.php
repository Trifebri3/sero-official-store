<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Production & Inventory Control') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F9F7F2]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:admin.inventory.dashboard />
        </div>
    </div>
</x-app-layout>
