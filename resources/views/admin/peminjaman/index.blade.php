@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Panel Admin')
@section('header-title', ' Manajemen Transaksi Peminjaman')

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
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-hand-holding text-blue-600"></i> Daftar Transaksi Peminjaman
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $peminjaman->total() }}</span>
        </h3>
        
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari peminjam / status..." 
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.peminjaman.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-600 px-3 py-2 text-sm rounded-xl flex items-center transition">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.peminjaman.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm shadow-blue-100 flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Peminjaman
            </a>
        </div>
    </div>
<!-- 
    <div class="p-4">
        <a href="{{ route('admin.peminjaman.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm shadow-blue-100 flex items-center gap-2 w-fit">
            <i class="fas fa-plus"></i> Tambah Peminjaman
        </a>
    </div> -->

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Peminjam</th>
                    <th class="py-3.5 px-4">Alat Dipinjam</th>
                    <th class="py-3.5 px-4">Tgl Pinjam / Rencana</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($peminjaman as $item)
                    <tr class="hover:bg-gray-50/70 transition align-top">
                        <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                        <td class="py-3 px-4 font-medium text-gray-800">
                            {{ $item->user->name ?? '-' }}
                            <div class="text-xs text-gray-400">{{ $item->user->email ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            @foreach($item->detailPinjam as $detail)
                                <span class="inline-block bg-gray-100 text-gray-700 px-2 py-0.5 rounded-lg text-xs mr-1 mb-1">
                                    {{ $detail->alat->nama_alat ?? 'Alat' }}
                                    <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-md text-[10px]">{{ $detail->jumlah }} pcs</span>
                                </span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-600">
                            <div>Pinjam: {{ $item->tgl_pinjam }}</div>
                            <div class="font-semibold text-gray-700">Rencana: {{ $item->tgl_kembali ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($item->status == 'diajukan') bg-yellow-100 text-yellow-700
                                @elseif($item->status == 'dipinjam') bg-blue-100 text-blue-700
                                @elseif($item->status == 'dikembalikan') bg-emerald-100 text-emerald-700
                                @elseif($item->status == 'telat') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-600 @endif">
                                <i class="fas fa-circle mr-1 text-[6px] align-middle"></i>
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex flex-col items-center gap-1.5">
                                <form action="{{ route('admin.peminjaman.updateStatus', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 hover:bg-gray-100 transition">
                                        <option value="diajukan" {{ $item->status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                        <option value="dipinjam" {{ $item->status == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                        <option value="dikembalikan" {{ $item->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                        <option value="telat" {{ $item->status == 'telat' ? 'selected' : '' }}>Telat</option>
                                    </select>
                                </form>

                                <form action="{{ route('admin.peminjaman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus peminjaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1 w-full justify-center">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400">
                            <i class="fas fa-inbox text-4xl block mb-2 text-gray-300"></i>
                            Belum ada data peminjaman.
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