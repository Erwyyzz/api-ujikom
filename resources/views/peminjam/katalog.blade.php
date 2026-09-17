
@extends('layouts.peminjam')

@section('title', 'Katalog Alat')
@section('header-title', ' Katalog Alat Tersedia')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($alats as $alat)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-bold text-gray-800">{{ $alat->nama_alat }}</h4>
            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs font-semibold">
                {{ $alat->kategori->nama_kategori ?? 'Umum' }}
            </span>
        </div>
        <p class="text-sm text-gray-500">Stok: <span class="font-semibold text-gray-700">{{ $alat->stok }}</span></p>
        <p class="text-sm text-gray-500">Kondisi: 
            <span class="px-2 py-0.5 rounded-full text-xs 
                @if($alat->kondisi == 'baik') bg-green-100 text-green-700
                @elseif($alat->kondisi == 'rusak') bg-red-100 text-red-700
                @else bg-yellow-100 text-yellow-700 @endif">
                {{ ucfirst($alat->kondisi) }}
            </span>
        </p>
        <a href="{{ route('peminjam.peminjaman.create', $alat->id) }}" 
        class="mt-3 inline-block bg-blue-50/80 backdrop-blur-sm border border-blue-300/50 text-blue-700 font-semibold px-4 py-2.5 rounded-xl transition hover:bg-blue-600 hover:text-white hover:border-blue-600 w-full text-center text-sm">
            <i class=""></i> Ajukan Peminjaman
        </a>
    </div>
    @empty
    <div class="col-span-full text-center py-10 text-gray-400">
        <i class="fas fa-box-open text-4xl block mb-2 text-gray-300"></i>
        Belum ada alat tersedia.
    </div>
    @endforelse
</div>
@endsection