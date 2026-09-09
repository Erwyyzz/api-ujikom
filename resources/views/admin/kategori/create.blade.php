@extends('layouts.app')

@section('title', 'Tambah Kategori - Panel Admin')
@section('header-title', 'Tambah Kategori Baru')

@section('content')
@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.kategori.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Kategori</label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" 
                placeholder="Masukkan nama kategori" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="bg-[#E07A5F] hover:bg-[#D96A4F] text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Simpan
            </button>
            <a href="{{ route('admin.kategori.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
