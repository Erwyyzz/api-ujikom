@extends('layouts.app')

@section('title', 'Tambah Peminjaman - Panel Admin')
@section('header-title', 'Tambah Peminjaman Baru')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header Card -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-hand-holding text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Tambah Peminjaman</h3>
                    <p class="text-sm text-gray-500">Isi data peminjaman alat dengan lengkap</p>
                </div>
            </div>
        </div>

        <!-- Body Form -->
        <form action="{{ route('admin.peminjaman.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Peminjam -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-1"></i> Peminjam
                    </label>
                    <select name="user_id" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                        <option value="">-- Pilih Peminjam --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Pinjam -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Pinjam
                    </label>
                    <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    @error('tgl_pinjam') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Kembali -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-check text-blue-500 mr-1"></i> Rencana Kembali
                    </label>
                    <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan') }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    @error('tgl_kembali_plan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Pilih Alat -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toolbox text-blue-500 mr-1"></i> Pilih Alat & Jumlah
                    </label>

                    <div id="alat-list" class="space-y-3">
                        <div class="alat-item grid grid-cols-1 md:grid-cols-2 gap-3">
                            <select name="alat_id[]" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                                <option value="">-- Pilih Alat --</option>
                                @foreach($alats as $alat)
                                    <option value="{{ $alat->id }}">
                                        {{ $alat->nama_alat }} (Stok: {{ $alat->stok }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="number" name="jumlah[]" placeholder="Jumlah" min="1" required
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                        </div>
                    </div>

                    <button type="button" onclick="tambahAlat()"
                        class="mt-3 bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Alat
                    </button>
                </div>

            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.peminjaman.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Peminjaman
                </button>
            </div>

        </form>
    </div>

</div>

<script>
    function tambahAlat() {
        const container = document.getElementById('alat-list');
        const newItem = document.createElement('div');
        newItem.className = 'alat-item grid grid-cols-1 md:grid-cols-2 gap-3';
        newItem.innerHTML = `
            <select name="alat_id[]" required
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                <option value="">-- Pilih Alat --</option>
                @foreach($alats as $alat)
                    <option value="{{ $alat->id }}">
                        {{ $alat->nama_alat }} (Stok: {{ $alat->stok }})
                    </option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <input type="number" name="jumlah[]" placeholder="Jumlah" min="1" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                <button type="button" onclick="this.parentElement.parentElement.remove()"
                    class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-2 rounded-xl text-sm font-semibold transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newItem);
    }
</script>
@endsection