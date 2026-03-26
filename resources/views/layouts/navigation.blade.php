<nav x-data="{ open: false, activeMenu: null }" class="bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-105">
                        <x-application-logo class="block h-10 w-auto fill-current text-luxury-dark" />
                    </a>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ __('Overview') }}</span>
                    </x-nav-link>

                    <div class="relative flex items-center" x-data="{ openInv: false }" @mouseenter="openInv = true" @mouseleave="openInv = false">
                        <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-luxury-gold transition-all cursor-default">
                            Collection
                            <svg class="ms-1 w-3 h-3 transition-transform" :class="openInv ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                        </button>

                        <div x-show="openInv" x-transition class="absolute top-full left-0 w-48 bg-white shadow-2xl rounded-2xl border border-gray-50 py-3 z-50">
                            <x-dropdown-link :href="route('admin.products.index')" class="font-serif italic">{{ __('Luxury Inventory') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.categories.index')" class="font-serif italic">{{ __('Categories') }}</x-dropdown-link>
                        </div>
                    </div>

                    <div class="relative flex items-center" x-data="{ openFin: false }" @mouseenter="openFin = true" @mouseleave="openFin = false">
                        <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-luxury-gold transition-all cursor-default">
                            Financials
                            <svg class="ms-1 w-3 h-3 transition-transform" :class="openFin ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                        </button>

                        <div x-show="openFin" x-transition class="absolute top-full left-0 w-56 bg-white shadow-2xl rounded-2xl border border-gray-50 py-3 z-50">
                            <div class="px-4 py-1 text-[8px] font-black text-gray-300 uppercase tracking-widest">Core Operations</div>
                            <x-dropdown-link :href="route('admin.transactions.dashboard')" class="font-serif italic text-luxury-gold">{{ __('Master Dashboard') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.transactions.pos')" class="font-serif italic">{{ __('POS Terminal') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.transactions.history')" class="font-serif italic">{{ __('Digital Vault') }}</x-dropdown-link>
                            <hr class="my-2 border-gray-50">
                            <x-dropdown-link :href="route('admin.financial.goal')" class="font-serif italic">{{ __('Goal Blueprint') }}</x-dropdown-link>
                        </div>
                    </div>

                    <div class="relative flex items-center" x-data="{ openOps: false }" @mouseenter="openOps = true" @mouseleave="openOps = false">
                        <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-luxury-gold transition-all cursor-default">
                            Operations
                            <svg class="ms-1 w-3 h-3 transition-transform" :class="openOps ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                        </button>

                        <div x-show="openOps" x-transition class="absolute top-full left-0 w-56 bg-white shadow-2xl rounded-2xl border border-gray-50 py-3 z-50">
                            <x-dropdown-link :href="route('admin.inventory.index')" class="font-serif italic">{{ __('Intelligence') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.inventory.tracking')" class="font-serif italic">{{ __('Live Movement') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.inventory.production')" class="font-serif italic">{{ __('Production') }}</x-dropdown-link>
                        </div>
                    </div>

                    <div class="relative flex items-center" x-data="{ openMark: false }" @mouseenter="openMark = true" @mouseleave="openMark = false">
                        <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-luxury-gold transition-all cursor-default">
                            Creative
                            <svg class="ms-1 w-3 h-3 transition-transform" :class="openMark ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3"/></svg>
                        </button>

                        <div x-show="openMark" x-transition class="absolute top-full left-0 w-56 bg-white shadow-2xl rounded-2xl border border-gray-50 py-3 z-50">
                            <x-dropdown-link :href="route('admin.marketing.studio')" class="font-serif italic text-luxury-gold">{{ __('Studio Creative') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.marketing.index')" class="font-serif italic">{{ __('Campaign DB') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.marketing.templates')" class="font-serif italic">{{ __('Form Blueprint') }}</x-dropdown-link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 px-4 py-2 bg-gray-50 rounded-2xl border border-transparent hover:border-luxury-gold transition-all duration-300 group">
                            <div class="text-right">
                                <p class="text-[9px] font-black text-luxury-dark uppercase tracking-widest leading-none">{{ Auth::user()->name }}</p>
                                <p class="text-[8px] text-gray-400 font-serif italic">Administrator</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-luxury-dark text-luxury-gold flex items-center justify-center text-[10px] font-black shadow-lg group-hover:bg-black transition-colors">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile Settings') }}</x-dropdown-link>
                        <x-dropdown-link href="/">{{ __('Explore Website') }}</x-dropdown-link>
                        <hr class="border-gray-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <span class="text-red-400">{{ __('Secure Logout') }}</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 text-luxury-dark rounded-xl bg-gray-50">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#FDFBF9] border-t border-gray-100 h-screen overflow-y-auto">
        <div class="pt-4 pb-6 space-y-4">
            <div class="px-6">
                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-3">Collection</p>
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('admin.products.index')">Inventory</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.categories.index')">Categories</x-responsive-nav-link>
                </div>
            </div>

            <div class="px-6">
                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-3">Financial Operations</p>
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('admin.transactions.dashboard')">Dashboard</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.transactions.pos')">POS Terminal</x-responsive-nav-link>
                </div>
            </div>

            <div class="px-6">
                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest mb-3">Creative & Marketing</p>
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('admin.marketing.studio')">Studio</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.marketing.index')">Database</x-responsive-nav-link>
                </div>
            </div>
        </div>

        <div class="pt-4 pb-20 border-t border-gray-200 bg-white">
            <div class="px-6 flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-full bg-luxury-dark text-luxury-gold flex items-center justify-center font-black">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-sm font-serif italic text-luxury-dark">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
