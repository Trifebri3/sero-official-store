<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb Luxury --}}
            <div class="mb-8 px-4 flex items-center gap-4">
                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-400">YOTA SYSTEM</span>
                <div class="h-[1px] w-8 bg-[#C5A358]"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.4em] text-black">Task Architect</span>
            </div>

            {{-- Memanggil Komponen Livewire Volt --}}
            @livewire('admin.todo.dynamic-todo')

        </div>
    </div>
</x-app-layout>
