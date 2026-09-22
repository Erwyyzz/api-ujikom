
@extends('layouts.peminjam')

@section('title', 'Ajukan Peminjaman')
@section('header-title', ' Form Ajukan Peminjaman')

@section('content')
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('peminjam.peminjaman.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Alat</label>
            <select name="alat_id" required class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- Pilih Alat --</option>
                @foreach($alats as $item)
                    <option value="{{ $item->id }}" {{ isset($alat) && $alat->id == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_alat }} (Stok: {{ $item->stok }})
                    </option>
                @endforeach
            </select>
            @error('alat_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Jumlah</label>
            <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required
                class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            @error('jumlah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Rencana Kembali</label>
            <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan', date('Y-m-d', strtotime('+3 days'))) }}" required
                class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            @error('tgl_kembali_plan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('peminjam.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">Ajukan</button>
        </div>
    </form>
</div>
@endsection