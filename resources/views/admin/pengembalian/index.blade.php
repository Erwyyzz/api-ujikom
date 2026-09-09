@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', ' Manajemen Pengembalian Alat')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 text-emerald-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2 text-emerald-500"></i>{{ session('success') }}</span>
        <button type="button" class="text-emerald-600 hover:text-emerald-800" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-exclamation-circle mr-2 text-red-500"></i>{{ session('error') }}</span>
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-undo-alt text-[#E07A5F]"></i> Daftar Pengembalian
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $pengembalians->count() }}</span>
        </h3>
        <a href="{{ route('admin.pengembalian.create') }}" class="bg-[#E07A5F] hover:bg-[#D96A4F] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm shadow-blue-100 flex items-center gap-2">
            <i class="fas fa-plus"></i> Proses Pengembalian
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Peminjam</th>
                    <th class="py-3.5 px-4">Tgl Kembali</th>
                    <th class="py-3.5 px-4">Kondisi</th>
                    <th class="py-3.5 px-4">Denda</th>
                    <th class="py-3.5 px-4">Petugas</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($pengembalians as $key => $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $item->peminjaman->user->name ?? '-' }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_kembali }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($item->kondisi_kembali == 'baik') bg-emerald-100 text-emerald-700
                            @elseif($item->kondisi_kembali == 'rusak') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            <i class="fas fa-circle mr-1 text-[6px] align-middle"></i>
                            {{ $item->kondisi_kembali }}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-semibold text-red-600">
                        @if($item->denda > 0)
                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-3 px-4">{{ $item->petugas->name ?? '-' }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('admin.pengembalian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data pengembalian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl block mb-2 text-gray-300"></i>
                        Belum ada data pengembalian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $pengembalians->links() }}
</div>
</div>
@endsection