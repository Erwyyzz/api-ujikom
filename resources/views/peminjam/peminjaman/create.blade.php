@extends('layouts.peminjam')

@section('title', 'Ajukan Peminjaman')
@section('header-title', 'Form Ajukan Peminjaman')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Notifikasi Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Card Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header Card --}}
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-hand-holding text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Ajukan Peminjaman</h3>
                    <p class="text-sm text-gray-500">Lengkapi data peminjaman alat</p>
                </div>
            </div>
        </div>

        {{-- Body Form --}}
        <form action="{{ route('peminjam.peminjaman.store') }}" method="POST" class="p-6">
            @csrf

            {{-- Hidden input alat_id --}}
            <input type="hidden" name="alat_id" value="{{ $alat->id }}">

            {{-- ====== INFO ALAT YANG DIPILIH ====== --}}
            <div class="mb-5 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div class="flex items-center gap-4">
                    {{-- Gambar Alat --}}
                    @if($alat->gambar)
                        <img src="{{ asset('uploads/alats/' . $alat->gambar) }}" 
                             alt="{{ $alat->nama_alat }}" 
                             class="w-20 h-20 object-cover rounded-xl border border-gray-200">
                    @else
                        <div class="w-20 h-20 bg-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-xl"></i>
                        </div>
                    @endif

                    {{-- Info Alat --}}
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $alat->nama_alat }}</h4>
                        <p class="text-xs text-gray-500 mb-1">
                            <i class="fas fa-tag text-blue-500 mr-1"></i>
                            {{ $alat->kategori->nama_kategori ?? 'Umum' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Kondisi: 
                            <span class="font-semibold
                                @if($alat->kondisi == 'baik') text-green-600
                                @elseif($alat->kondisi == 'rusak') text-red-600
                                @else text-yellow-600 @endif">
                                {{ ucfirst($alat->kondisi) }}
                            </span>
                        </p>
                    </div>

                    {{-- Stok --}}
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Stok Tersedia</p>
                        <p class="text-2xl font-bold 
                            {{ $alat->stok > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $alat->stok }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ====== INPUT JUMLAH ====== --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sort-numeric-up text-blue-500 mr-1"></i> Jumlah yang Dipinjam
                </label>
                <input type="number" name="jumlah" id="jumlah" 
                    value="{{ old('jumlah', ) }}" 
                    min="1" 
                    max="{{ $alat->stok }}"
                    data-stok="{{ $alat->stok }}"
                    required
                    placeholder="Masukan jumlah alat"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                @error('jumlah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- ====== INFO STOK REALTIME ====== --}}
            <div id="infoStok" class="mb-5 p-4 rounded-xl border hidden">
                <div id="infoStokContent"></div>
            </div>

            {{-- ====== TANGGAL RENCANA KEMBALI ====== --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Rencana Kembali
                </label>
                <input type="date" name="tgl_kembali_plan" 
                    value="{{ old('tgl_kembali_plan', date('Y-m-d', strtotime('+3 days'))) }}" 
                    required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                <p class="text-xs text-gray-400 mt-1">Pilih tanggal pengembalian alat</p>
                @error('tgl_kembali_plan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- ====== TOMBOL AKSI ====== --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('peminjam.dashboard') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" id="btnAjukan"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // REALTIME INFO STOK
    // 
    // Fungsi ini dipanggil SETIAP KALI user ngetik di input jumlah.
    // 
    // Logika:
    // - Kalo jumlah <= stok  → tampil info HIJAU "Stok mencukupi"
    // - Kalo jumlah > stok   → tampil info MERAH "Stok tidak mencukupi"
    const jumlahInput = document.getElementById('jumlah');
    const infoStok = document.getElementById('infoStok');
    const infoStokContent = document.getElementById('infoStokContent');
    const btnAjukan = document.getElementById('btnAjukan');

    // Ambil stok dari data-attribute
    const stokTersedia = parseInt(jumlahInput.dataset.stok) || 0;

    // Fungsi untuk cek stok realtime
    function cekStok() {
        const jumlah = parseInt(jumlahInput.value) || 0;

        // Kalo input kosong, sembunyikan info
        if (!jumlah) {
            infoStok.classList.add('hidden');
            btnAjukan.disabled = false;
            return;
        }

        // KONDISI 1: Jumlah > stok → ERROR MERAH
        if (jumlah > stokTersedia) {
            infoStokContent.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    <div>
                        <p class="font-bold text-red-700">Stok Tidak Mencukupi!</p>
                        <p class="text-sm text-red-600">
                            Anda minta: <strong>${jumlah}</strong> unit, 
                            stok tersedia: <strong>${stokTersedia}</strong> unit
                        </p>
                        <p class="text-xs text-red-500 mt-1">Kurangi jumlah peminjaman.</p>
                    </div>
                </div>
            `;
            infoStok.className = 'mb-5 p-4 rounded-xl border bg-red-50 border-red-200';
            infoStok.classList.remove('hidden');
            
            // Disable tombol ajukan
            btnAjukan.disabled = true;
        } 
        // KONDISI 2: Jumlah <= stok → SUKSES HIJAU
        else {
            infoStokContent.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    <div>
                        <p class="font-bold text-green-700">Stok Mencukupi</p>
                        <p class="text-sm text-green-600">
                            Anda minta: <strong>${jumlah}</strong> unit dari 
                            <strong>${stokTersedia}</strong> unit tersedia
                        </p>
                    </div>
                </div>
            `;
            infoStok.className = 'mb-5 p-4 rounded-xl border bg-green-50 border-green-200';
            infoStok.classList.remove('hidden');
            
            // Enable tombol ajukan
            btnAjukan.disabled = false;
        }
    }

    // Event Listener: setiap kali user ngetik di input jumlah,
    // panggil fungsi cekStok()
    jumlahInput.addEventListener('input', cekStok);

    // Jalankan cek stok saat halaman pertama load
    document.addEventListener('DOMContentLoaded', cekStok);
</script>
@endsection