<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif text-2xl italic text-luxury-dark uppercase tracking-tighter">
            Creative <span class="text-luxury-gold">Studio</span>
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

            @if (session()->has('message'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-[10px] font-bold uppercase tracking-widest rounded-r-xl animate-bounce">
                    {{ session('message') }}
                </div>
            @endif

            @livewire('admin.marketing.studio')

        </div>
    </div>
</x-app-layout>
