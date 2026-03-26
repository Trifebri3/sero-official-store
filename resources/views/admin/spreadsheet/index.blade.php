@extends('layouts.app') {{-- Sesuaikan dengan nama layout admin kamu --}}

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Header Breadcrumb (Aestetik Sero) --}}
        <div class="mb-8 px-4 flex items-center gap-4">
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-400">Masterpiece</span>
            <div class="h-[1px] w-8 bg-[#C5A358]"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.4em] text-black">Vault Architect</span>
        </div>

        {{-- Memanggil Livewire Volt Component --}}
        <livewire:admin.kolom.spreadsheet-manager />

    </div>
</div>
@endsection
