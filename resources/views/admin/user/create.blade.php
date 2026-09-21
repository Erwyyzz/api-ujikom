@extends('layouts.app')

@section('title', 'Tambah User - Panel Admin')
@section('header-title', ' Tambah Pengguna Baru')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header Card -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-user-plus text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Tambah Pengguna</h3>
                    <p class="text-sm text-gray-500">Isi data pengguna dengan lengkap dan benar</p>
                </div>
            </div>
        </div>

        <!-- Body Form -->
        <form action="{{ route('admin.user.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Nama Lengkap -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-1"></i> Nama Lengkap
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Masukan Nama..."
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-blue-500 mr-1"></i> Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="nama@gmail.com"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- No HP -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone text-blue-500 mr-1"></i> No HP
                    </label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        placeholder="081234567890"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    @error('no_hp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Role -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-blue-500 mr-1"></i> Role / Hak Akses
                    </label>
                    <select name="role" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="peminjam" {{ old('role') == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-500 mr-1"></i> Password
                    </label>
                    <input type="password" name="password" required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-blue-500 mr-1"></i> Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Ulangi password"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                </div>

            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.user.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan User
                </button>
            </div>

        </form>

    </div>

</div>
@endsection