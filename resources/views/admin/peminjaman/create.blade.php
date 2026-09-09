@extends('layouts.app')

@section('title', 'Tambah Peminjaman - Panel Admin')
@section('header-title', 'Tambah Peminjaman Baru')

@section('content')
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-3xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.peminjaman.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Peminjam</label>
            <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Peminjam --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_pinjam') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Kembali (Rencana)</label>
                <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tgl_kembali_plan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Alat & Jumlah</label>
            <div id="alat-list">
                <div class="alat-item grid grid-cols-2 gap-4 mb-2">
                    <select name="alat_id[]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($alats as $alat)
                            <option value="{{ $alat->id }}">
                                {{ $alat->nama_alat }} (Stok: {{ $alat->stok }})
                            </option>
                        @endforeach
                    </select>
                    <input type="number" name="jumlah[]" placeholder="Jumlah" min="1" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <button type="button" onclick="tambahAlat()" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded text-sm font-semibold transition mt-2">
                + Tambah Alat
            </button>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.peminjaman.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
            <button type="submit" class="bg-[#E07A5F] hover:bg-[#D96A4F] text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
    function tambahAlat() {
        const container = document.getElementById('alat-list');
        const newItem = document.createElement('div');
        newItem.className = 'alat-item grid grid-cols-2 gap-4 mb-2';
        newItem.innerHTML = `
            <select name="alat_id[]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Alat --</option>
                @foreach($alats as $alat)
                    <option value="{{ $alat->id }}">
                        {{ $alat->nama_alat }} (Stok: {{ $alat->stok }})
                    </option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <input type="number" name="jumlah[]" placeholder="Jumlah" min="1" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-semibold transition">
                    ×
                </button>
            </div>
        `;
        container.appendChild(newItem);
    }
</script>
@endsection
