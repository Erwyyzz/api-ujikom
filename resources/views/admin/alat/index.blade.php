@extends('layouts.app')

@section('title', 'Kelola Alat - Panel Admin')
@section('header-title', ' Manajemen Data Alat')

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
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-toolbox text-[#E07A5F]"></i> Daftar Alat
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $alats->total() }}</span>
        </h3>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.alat.index') }}" method="GET" class="flex w-full md:w-72">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari nama alat, kategori..." 
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-[#E07A5F] focus:border-transparent">
                <button type="submit" class="bg-[#E07A5F] hover:bg-[#D96A4F] text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.alat.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-600 px-3 py-2 text-sm rounded-xl flex items-center transition">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            
            <a href="{{ route('admin.alat.create') }}" class="bg-[#E07A5F] hover:bg-[#D96A4F] text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm shadow-blue-100 flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Alat
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">Gambar</th>
                    <th class="py-3.5 px-4">Nama Alat</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4">Stok</th>
                    <th class="py-3.5 px-4">Kondisi</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($alats as $alat)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4">
                        @if($alat->gambar)
                            <img src="{{ asset('uploads/alats/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-11 h-11 object-cover rounded-xl border border-gray-200 shadow-sm" />
                        @else
                            <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-xs border border-gray-200">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $alat->nama_alat }}</td>
                    <td class="py-3 px-4">
                        <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full text-xs font-medium">{{ $alat->kategori->nama_kategori ?? '-' }}</span>
                    </td>
                    <td class="py-3 px-4 font-semibold">{{ $alat->stok }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            @if(strtolower($alat->kondisi) == 'baik') bg-emerald-100 text-emerald-700
                            @elseif(strtolower($alat->kondisi) == 'rusak') bg-red-100 text-red-700
                            @else bg-amber-100 text-amber-700 @endif">
                            <i class="fas fa-circle mr-1 text-[6px] align-middle"></i>
                            {{ $alat->kondisi }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.alat.edit', $alat->id) }}" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" onsubmit="return confirm('Yakin hapus alat ini?')">
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
                    <td colspan="6" class="py-10 text-center text-gray-400">
                        <i class="fas fa-box-open text-4xl block mb-2 text-gray-300"></i>
                        Belum ada data alat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $alats->links() }}
</div>
</div>
@endsection