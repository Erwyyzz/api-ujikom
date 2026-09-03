@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')
@section('header-title', ' Dashboard Petugas')

@section('content')
<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Pengajuan Peminjaman</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalPengajuan }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Sedang Dipinjam</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalDipinjam }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-hand-holding text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Selesai Dikembalikan</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalDikembalikan }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Selamat Datang + Informasi Cepat -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 card-hover">
        <h4 class="text-sm font-semibold text-gray-600 mb-2">👋 Selamat Datang, <span class="text-gray-900">{{ auth()->user()->name }}</span></h4>
        <p class="text-sm text-gray-400">Anda login sebagai <span class="font-medium text-green-600">Petugas</span></p>
        <hr class="my-3">
        <div class="text-sm text-gray-500">
            <p><i class="fas fa-check-circle text-green-500 mr-2"></i> Anda memiliki <strong>{{ $totalPengajuan }}</strong> pengajuan peminjaman yang perlu disetujui.</p>
            <p><i class="fas fa-info-circle text-blue-500 mr-2"></i> Ada <strong>{{ $totalDipinjam }}</strong> alat yang sedang dipinjam.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 card-hover">
        <h4 class="text-sm font-semibold text-gray-600 mb-2">⚡ Aksi Cepat</h4>
        <div class="space-y-2">
            <a href="{{ route('petugas.peminjaman.index') }}" class="block text-sm text-blue-600 hover:text-blue-800 transition">
                <i class="fas fa-arrow-right mr-2"></i> Lihat pengajuan peminjaman
            </a>
            <a href="{{ route('petugas.pengembalian.index') }}" class="block text-sm text-blue-600 hover:text-blue-800 transition">
                <i class="fas fa-arrow-right mr-2"></i> Lihat riwayat pengembalian
            </a>
            <a href="{{ route('petugas.laporan') }}" class="block text-sm text-blue-600 hover:text-blue-800 transition">
                <i class="fas fa-arrow-right mr-2"></i> Cetak laporan peminjaman
            </a>
        </div>
    </div>
</div>
@endsection