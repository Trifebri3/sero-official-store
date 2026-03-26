<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'SERO LIVING | Curator Dashboard' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Custom Scrollbar untuk vibe Luxury */
            ::-webkit-scrollbar { width: 5px; }
            ::-webkit-scrollbar-track { background: #f1f1f1; }
            ::-webkit-scrollbar-thumb { background: #B4965A; border-radius: 10px; }
        </style>
    </head>
    <body class="font-sans antialiased text-luxury-dark selection:bg-luxury-gold selection:text-white">
        <div class="min-h-screen bg-[#FDFBF9]"> @include('layouts.navigation')

            @isset($header)
                <header class="bg-white/50 backdrop-blur-md border-b border-gray-100">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-4">
                            <div class="w-1 h-8 bg-luxury-gold rounded-full"></div>
                            <div class="font-serif italic text-2xl text-luxury-dark tracking-tight">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <main class="py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>

            <footer class="py-10 border-t border-gray-100 mt-20">
                <div class="max-w-7xl mx-auto px-8 flex justify-between items-center opacity-30">
                    <img src="{{ asset('images/logo.png') }}" alt="SERO LIVING" class="h-6 grayscale opacity-50">
                    <p class="text-[9px] font-black uppercase tracking-[0.4em]">© 2026 SERO LIVING CREATIVE</p>
                </div>
            </footer>
        </div>
    </body>
</html>
