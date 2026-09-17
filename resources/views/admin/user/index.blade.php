@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', ' Manajemen Pengguna')

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
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-users text-blue-600"></i> Daftar Pengguna Sistem
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $users->total() }}</span>
        </h3>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari nama, email, role..." 
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.user.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-600 px-3 py-2 text-sm rounded-xl flex items-center transition">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            
            <a href="{{ route('admin.user.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm shadow-blue-100 flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah User
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Nama</th>
                    <th class="py-3.5 px-4">Email</th>
                    <th class="py-3.5 px-4">Role</th>
                    <th class="py-3.5 px-4">No HP</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($users as $key => $user)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $users->firstItem() + $key }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $user->name }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($user->role == 'admin') bg-red-100 text-red-700
                            @elseif($user->role == 'petugas') bg-blue-100 text-blue-700
                            @else bg-green-100 text-green-700 @endif">
                            <i class="fas fa-circle mr-1 text-[6px] align-middle"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">{{ $user->no_hp ?? '-' }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?')">
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
                        <i class="fas fa-users-slash text-4xl block mb-2 text-gray-300"></i>
                        Tidak ada data user.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $users->links() }}
</div>
</div>
@endsection