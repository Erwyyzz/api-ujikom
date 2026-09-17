@extends('layouts.petugas')

@section('title', 'Cetak Laporan - Petugas')
@section('header-title', ' Laporan Peminjaman & Pengembalian Alat')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

    <!-- Filter Laporan -->
    <div class="mb-3">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Filter Laporan</h4>
        <form action="{{ route('petugas.laporan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-2">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status Peminjaman</label>
                <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal (Pinjam)</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal (Pinjam)</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                    <i class="fas fa-filter mr-0.5"></i> Filter
                </button>
                <a href="{{ route('petugas.laporan') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                    <i class="fas fa-undo"></i> Reset
                </a>
                <a href="{{ route('petugas.laporan.cetak') }}?status={{ request('status') }}&start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" 
                class="bg-green-600 hover:bg-green-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                    <i class="fas fa-file-pdf mr-0.5"></i> PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Hasil Rekap Laporan -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Peminjam</th>
                    <th class="py-3.5 px-4">Tgl Pinjam</th>
                    <th class="py-3.5 px-4">Rencana Kembali</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4">Detail Alat</th>
                    <th class="py-3.5 px-4">Denda</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($peminjaman as $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_pinjam }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_kembali_plan ?? '-' }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($item->status == 'diajukan') bg-yellow-100 text-yellow-700
                            @elseif($item->status == 'dipinjam') bg-blue-100 text-blue-700
                            @elseif($item->status == 'dikembalikan') bg-green-100 text-green-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            <span class="inline-block bg-gray-100 text-gray-700 px-2 py-0.5 rounded-lg text-xs mr-1 mb-1">
                                {{ $detail->alat->nama_alat ?? 'Alat' }} <span class="text-xs text-gray-400">({{ $detail->jumlah }})</span>
                            </span>
                        @endforeach
                    </td>
                    <td class="py-3 px-4 font-semibold text-red-600">
                        @if($item->pengembalian && $item->pengembalian->denda > 0)
                            Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl block mb-2 text-gray-300"></i>
                        Tidak ada data laporan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $peminjaman->links() }}
</div>
</div>
@endsection