@extends('layouts.peminjam')

@section('title', 'Dashboard Peminjam')
@section('header-title', 'Dashboard Peminjam')

@section('content')

{{-- ====== STATISTIK ====== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Menunggu Persetujuan</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $totalDiajukan ?? 0 }}</p>
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
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalDipinjam ?? 0 }}</p>
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
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalDikembalikan ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

{{-- ====== PERINGATAN PENGEMBALIAN ====== --}}
@if(isset($peringatan) && count($peringatan) > 0)
<div class="mb-6">
    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
        <i class="fas fa-bell text-yellow-500"></i> Peringatan Pengembalian
    </h4>
    <div class="space-y-2">
        @foreach($peringatan as $p)
            @if($p['status'] == 'telat')
                <a href="{{ route('peminjam.pengembalian.index') }}" class="block bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm hover:bg-red-100 transition">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔴</span>
                        <div>
                            <p class="font-semibold text-red-700">{{ $p['nama_alat'] }}</p>
                            <p class="text-sm text-red-600">Batas kembali: {{ $p['tgl_kembali_plan'] }} (telat {{ abs($p['selisih_hari']) }} hari)</p>
                        </div>
                    </div>
                </a>
            @elseif($p['status'] == 'segera')
                <a href="{{ route('peminjam.pengembalian.index') }}" class="block bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-xl shadow-sm hover:bg-yellow-100 transition">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <p class="font-semibold text-yellow-700">{{ $p['nama_alat'] }}</p>
                            <p class="text-sm text-yellow-600">Batas kembali: {{ $p['tgl_kembali_plan'] }} (tersisa {{ $p['selisih_hari'] }} hari)</p>
                        </div>
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- ====== KATALOG ALAT ====== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-toolbox text-blue-500"></i> Katalog Alat Tersedia
        </h4>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($alats as $alat)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover relative">

            {{-- Gambar Alat --}}
            <div class="relative mb-3">
                @if($alat->gambar)
                    <img src="{{ asset('uploads/alats/' . $alat->gambar) }}" 
                         alt="{{ $alat->nama_alat }}" 
                         class="w-full h-32 object-cover rounded-xl 
                                {{ $alat->stok <= 0 ? 'grayscale opacity-60' : '' }}">
                @else
                    <div class="w-full h-32 bg-gray-200 rounded-xl flex items-center justify-center text-gray-400
                                {{ $alat->stok <= 0 ? 'grayscale opacity-60' : '' }}">
                        <i class="fas fa-image text-2xl"></i>
                    </div>
                @endif

                {{-- Overlay Stok Habis --}}
                @if($alat->stok <= 0)
                    <div class="absolute inset-0 flex items-center justify-center rounded-xl">
                        <span class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-lg">
                            <i class="fas fa-times-circle mr-1"></i> STOK HABIS
                        </span>
                    </div>
                @endif
            </div>

            {{-- Nama & Kategori --}}
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-bold text-gray-800 text-sm">{{ $alat->nama_alat }}</h4>
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-lg text-xs font-semibold">
                    {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                </span>
            </div>

            {{-- Stok & Kondisi --}}
            <p class="text-sm text-gray-500">
                Stok: <span class="font-semibold {{ $alat->stok <= 0 ? 'text-red-600' : 'text-gray-700' }}">{{ $alat->stok }}</span>
            </p>
            <p class="text-sm text-gray-500 mb-3">
                Kondisi: 
                <span class="px-2 py-0.5 rounded-full text-xs 
                    @if($alat->kondisi == 'baik') bg-green-100 text-green-700
                    @elseif($alat->kondisi == 'rusak') bg-red-100 text-red-700
                    @else bg-yellow-100 text-yellow-700 @endif">
                    {{ ucfirst($alat->kondisi) }}
                </span>
            </p>

            {{-- Tombol Ajukan --}}
            @if($alat->stok > 0)
                <a href="{{ route('peminjam.peminjaman.create', $alat->id) }}" 
                   class="inline-block bg-blue-50 border border-blue-300/50 text-blue-700 font-semibold px-4 py-2.5 rounded-xl transition hover:bg-blue-600 hover:text-white hover:border-blue-600 w-full text-center text-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Ajukan Peminjaman
                </a>
            @else
                <button disabled 
                    class="inline-block bg-gray-200 text-gray-400 font-semibold px-4 py-2.5 rounded-xl w-full text-center text-sm cursor-not-allowed">
                    <i class="fas fa-ban mr-1"></i> Stok Habis
                </button>
            @endif

        </div>
        @empty
        <div class="col-span-full text-center py-10 text-gray-400">
            <i class="fas fa-box-open text-4xl block mb-2 text-gray-300"></i>
            Belum ada alat tersedia.
        </div>
        @endforelse
    </div>
</div>

{{-- ====== STATUS PEMINJAMAN AKTIF ====== --}}
@if(isset($riwayat) && $riwayat->count() > 0)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-list-ul text-blue-500"></i> Status Peminjaman Aktif
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Alat</th>
                    <th class="py-3.5 px-4">Jumlah</th>
                    <th class="py-3.5 px-4">Tgl Pinjam</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @foreach($riwayat as $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-800">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? 'Alat' }}
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->jumlah }}
                        @endforeach
                    </td>
                    <td class="py-3 px-4">{{ $item->tgl_pinjam }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($item->status == 'diajukan') bg-yellow-100 text-yellow-700
                            @elseif($item->status == 'dipinjam') bg-blue-100 text-blue-700
                            @elseif($item->status == 'menunggu_verifikasi') bg-purple-100 text-purple-700
                            @elseif($item->status == 'dikembalikan') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        @if($item->status == 'dipinjam')
                            <form action="{{ route('peminjam.kembalikan', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengembalikan alat ini?')">
                                @csrf
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-undo mr-1"></i> Kembalikan
                                </button>
                            </form>
                        @elseif($item->status == 'menunggu_verifikasi')
                            <span class="text-xs text-purple-600 font-semibold">Menunggu Verifikasi</span>
                        @elseif($item->status == 'diajukan')
                            <span class="text-xs text-yellow-600">Menunggu Persetujuan</span>
                        @else
                            <span class="text-xs text-gray-400">Selesai</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection