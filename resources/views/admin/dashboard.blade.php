@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')

<!-- Alert Selamat Datang -->
<div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 text-gray-700 p-5 rounded-2xl shadow-sm flex items-center justify-between flex-wrap gap-3">
    <div>
        <p class="text-sm">
            Selamat datang, <strong class="font-semibold text-gray-900">{{ auth()->user()->name }}</strong>! 
            Anda login sebagai 
            <span class="uppercase font-bold px-2 py-0.5 rounded-full text-xs 
                @if(auth()->user()->role == 'admin') bg-red-100 text-red-700
                @elseif(auth()->user()->role == 'petugas') bg-blue-100 text-blue-700
                @else bg-green-100 text-green-700 @endif">
                {{ auth()->user()->role }}
            </span>
        </p>
    </div>
    <div class="text-sm text-gray-400">
        <i class="far fa-calendar-alt mr-1"></i> {{ date('d F Y') }}
    </div>
</div>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Total Alat</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\Alat::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-toolbox text-[#E07A5F] text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Total User</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Peminjaman Aktif</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\Peminjaman::where('status', 'dipinjam')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-hand-holding text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-400 font-medium">Total Kategori</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\Kategori::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-tags text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Log Aktivitas -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history mr-2 text-[#E07A5F]"></i> Log Aktivitas Terbaru
        </h3>
        <span class="text-xs text-gray-400 bg-gray-200 px-3 py-1 rounded-full">10 terakhir</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Tanggal</th>
                    <th class="py-3 px-4 border-b">User</th>
                    <th class="py-3 px-4 border-b">Aktivitas</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($logs as $log)
                <tr class="table-row border-b border-gray-50">
                    <td class="py-3 px-4">
                        <span class="text-xs font-medium text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                    </td>
                    <td class="py-3 px-4 font-medium text-gray-800">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-[#E07A5F] text-xs font-bold">
                                {{ substr($log->user->name ?? 'U', 0, 1) }}
                            </div>
                            {{ $log->user->name ?? 'Sistem' }}
                        </div>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $log->aktivitas }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-6 text-center text-gray-400 text-sm">Belum ada aktivitas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection