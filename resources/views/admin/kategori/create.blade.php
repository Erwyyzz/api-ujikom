@extends('layouts.app')

@section('title', 'Tambah Kategori - Panel Admin')
@section('header-title', 'Tambah Kategori Baru')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header Card -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-tags text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Tambah Kategori</h3>
                    <p class="text-sm text-gray-500">Isi nama kategori alat dengan benar</p>
                </div>
            </div>
        </div>

        <!-- Body Form -->
        <form action="{{ route('admin.kategori.store') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag text-blue-500 mr-1"></i> Nama Kategori
                </label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required
                    placeholder="Contoh: Elektronik"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                @error('nama_kategori') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.kategori.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>

</div>
@endsection