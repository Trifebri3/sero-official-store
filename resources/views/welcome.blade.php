<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.header')
</head>
<body class="bg-[#F5F2ED] font-sans antialiased text-[#1C1C1C]">

<nav class="fixed top-0 w-full z-50 bg-[#F5F2ED]/80 backdrop-blur-2xl border-b border-black/5 px-8 md:px-12 h-24 flex justify-between items-center transition-all duration-500">
    <div class="flex items-center">
        <img src="{{ asset('images/logo.png') }}" alt="SERO LIVING" class="h-10 md:h-24 w-auto object-contain">
    </div>

    <div class="hidden md:flex gap-12 text-[10px] font-black uppercase tracking-[0.4em] text-[#1C1C1C]/60">
        <a href="#collection" class="hover:text-[#C5A358] transition-colors">Collection</a>
        <a href="#story" class="hover:text-[#C5A358] transition-colors">Our Story</a>
        <a href="https://s.shopee.co.id/W2DatWTIn" target="_blank" class="hover:text-[#C5A358] transition-colors">Shop</a>
    </div>

    <div class="flex items-center gap-6">
        <a href="https://s.shopee.co.id/W2DatWTIn" target="_blank" class="bg-[#1C1C1C] text-[#C5A358] px-8 py-3.5 rounded-full text-[9px] font-black uppercase tracking-[0.3em] hover:bg-[#C5A358] hover:text-white transition-all shadow-xl shadow-black/10">
            Order Now
        </a>
    </div>
</nav>

<section class="relative min-h-[90vh] md:min-h-screen flex items-center px-6 pt-24 md:pt-32 bg-[#FDFBF9]">
    <div class="max-w-7xl mx-auto w-full grid md:grid-cols-2 gap-10 md:gap-16 items-center">

        <div class="space-y-8 order-2 md:order-1">
            <div class="space-y-4">
                <span class="text-[#C5A358] text-[11px] font-black uppercase tracking-[0.6em] block">Sero Creative Studio</span>
                <h1 class="text-5xl md:text-8xl font-serif italic leading-[1.1] tracking-tight text-[#1C1C1C]">
                    Elevate your <br>
                    <span class="text-[#C5A358]">Corner</span> with <br>
                    Aesthetic Soul.
                </h1>
            </div>

            <p class="text-sm md:text-base text-gray-500 max-w-sm font-light leading-relaxed tracking-wide">
                Mengkurasi dekorasi unik dan aksesoris estetik yang mengubah setiap sudut ruangan menjadi galeri pribadi yang menenangkan.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <a href="#collection" class="bg-[#1C1C1C] text-white px-10 py-5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] text-center hover:bg-[#C5A358] hover:scale-105 transition-all duration-500 shadow-xl">
                    Explore Gallery
                </a>
                <a href="https://s.shopee.co.id/W2DatWTIn" target="_blank" class="border border-black/10 text-[#1C1C1C] px-10 py-5 rounded-full text-[10px] font-black uppercase tracking-[0.2em] text-center hover:bg-white transition-all duration-500">
                    Shop on Shopee
                </a>
            </div>
        </div>

        <div class="order-1 md:order-2">
            <div class="relative group">
                <div class="absolute -top-6 -right-6 w-full h-full border border-[#C5A358]/20 rounded-[4rem] -z-10 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-1000"></div>

                <div class="h-[55vh] md:h-[75vh] rounded-[3.5rem] md:rounded-[4.5rem] overflow-hidden shadow-2xl border-[6px] border-white">
                    <img src="{{ asset('images/foto1.png') }}"
                         class="w-full h-full object-cover grayscale-[5%] group-hover:scale-110 transition-transform duration-[3s]"
                         alt="Sero Aesthetic Collection">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-60"></div>
                </div>

                <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-[2rem] shadow-xl hidden md:block">
                    <p class="text-[10px] font-black uppercase tracking-widest text-[#C5A358]">Curated Piece</p>
                    <p class="font-serif italic text-lg text-[#1C1C1C]">Limited Edition 2026</p>
                </div>
            </div>
        </div>

    </div>
</section>



<section id="story" class="py-24 md:py-40 px-6 bg-[#FDFBF9]"> <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-12 gap-10 md:gap-16 items-center">

            <div class="md:col-span-5 relative order-2 md:order-1 mt-12 md:mt-0">
                <div class="aspect-[3/4] rounded-[3rem] md:rounded-[4rem] overflow-hidden shadow-2xl relative z-10 border-4 border-white">
                    <img src="{{ asset('images/foto2.png') }}"
                         class="w-full h-full object-cover transition-transform duration-1000 hover:scale-105"
                         alt="Sero Aesthetic Decor">
                </div>

            </div>

            <div class="md:col-span-7 space-y-8 md:pl-12 order-1 md:order-2">
                <div class="space-y-3">
                    <span class="text-[#C5A358] text-[11px] font-black uppercase tracking-[0.5em] block">The Philosophy</span>
                    <h2 class="text-4xl md:text-7xl font-serif italic leading-[1.2] text-[#1C1C1C] tracking-tight">
                        Kemewahan Tidak <br>
                        <span class="text-gray-400">Harus Bersuara Keras.</span>
                    </h2>
                </div>

                <div class="space-y-5 text-gray-600 text-base md:text-lg leading-relaxed font-light tracking-wide max-w-xl">
                    <p>
                        SERO LIVING lahir dari keinginan untuk mengkurasi keindahan kecil yang sering terlewatkan. Kami fokus pada dekorasi unik dan aksesoris estetik seperti vas bunga mini yang artistik atau nampan dekoratif yang elegan yang dirancang untuk menghidupkan sudut-sudut mati di rumah Anda.
                    </p>
                    <p class="font-normal text-[#1C1C1C]/80">
                        Filosofi kami adalah menghadirkan produk dengan desain premium namun tetap terjangkau. Bagi kami, "murah" bukan berarti murahan. Setiap piece dikurasi dengan teliti untuk memastikan kualitas visual yang tinggi, menghadirkan harmoni dan ketenangan visual tanpa harus menguras kantong Anda.
                    </p>
                </div>

                <a href="https://s.shopee.co.id/W2DatWTIn" target="_blank" class="pt-6 flex items-center gap-4 group cursor-pointer inline-flex">
                    <div class="w-10 h-[1px] bg-black group-hover:w-16 group-hover:bg-[#C5A358] transition-all duration-500"></div>
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-[#1C1C1C] group-hover:text-[#C5A358] transition-colors">
                        Discover Unique Decor
                    </span>
                </a>
            </div>

        </div>
    </div>
</section>

<section id="collection" class="py-32 bg-white rounded-t-[5rem] px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-20 space-y-4">
            <span class="text-[#C5A358] text-[11px] font-black uppercase tracking-[0.6em]">Curated Selection</span>
            <h2 class="text-5xl md:text-7xl font-serif italic">Our Gallery</h2>
        </div>

        <livewire:public-gallery />
    </div>
</section>

    @include('partials.footer')
</body>
</html>
