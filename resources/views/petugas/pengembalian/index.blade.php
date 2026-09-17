@extends('layouts.petugas')

@section('title', 'Verifikasi Pengembalian - Petugas')
@section('header-title', ' Verifikasi Pengembalian Alat')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 text-emerald-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2 text-emerald-500"></i>{{ session('success') }}</span>
        <button type="button" class="text-emerald-600 hover:text-emerald-800" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-exclamation-circle mr-2 text-red-500"></i>{{ session('error') }}</span>
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-clipboard-check text-blue-500"></i> Verifikasi Pengembalian
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $pengembalian->count() }}</span>
        </h3>
        <p class="text-sm text-gray-400 mt-1">Daftar pengembalian yang menunggu verifikasi petugas</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Peminjam</th>
                    <th class="py-3.5 px-4">Alat</th>
                    <th class="py-3.5 px-4">Jumlah</th>
                    <th class="py-3.5 px-4">Rencana Kembali</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($pengembalian as $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? 'Alat' }}
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->jumlah }}
                        @endforeach
                    </td>
                    <td class="py-3 px-4">{{ $item->tgl_kembali_plan ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">
                        <a href="{{ route('petugas.pengembalian.verifikasi', $item->id) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition inline-block">
                            <i class="fas fa-check-circle mr-1"></i> Verifikasi
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl block mb-2 text-gray-300"></i>
                        Tidak ada pengembalian yang menunggu verifikasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $pengembalian->links() }}
</div>
</div>
@endsection