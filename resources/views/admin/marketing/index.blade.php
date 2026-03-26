<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl italic text-luxury-dark uppercase tracking-tighter">
            Marketing <span class="text-luxury-gold">Database</span>
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F9F7F2] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex gap-8 mb-10 border-b border-gray-100 pb-4">
                <a href="{{ route('admin.marketing.index') }}"
                   class="text-[10px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('admin.marketing.index') ? 'text-luxury-gold border-b-2 border-luxury-gold' : 'text-gray-400 hover:text-luxury-dark' }} pb-4 transition-all">
                   Content Assets
                </a>
                <a href="{{ route('admin.marketing.studio') }}"
                   class="text-[10px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('admin.marketing.studio') ? 'text-luxury-gold border-b-2 border-luxury-gold' : 'text-gray-400 hover:text-luxury-dark' }} pb-4 transition-all">
                   Creative Studio
                </a>
                <a href="{{ route('admin.marketing.templates') }}"
                   class="text-[10px] font-bold uppercase tracking-[0.2em] {{ request()->routeIs('admin.marketing.templates') ? 'text-luxury-gold border-b-2 border-luxury-gold' : 'text-gray-400 hover:text-luxury-dark' }} pb-4 transition-all">
                   Form Config
                </a>
            </div>

            <livewire:admin.marketing.index />

        </div>
    </div>
    <div>
    <livewire:admin.marketing.detail />
</div>
</x-app-layout>
