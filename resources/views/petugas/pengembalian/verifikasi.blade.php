@extends('layouts.petugas')

@section('title', 'Verifikasi Pengembalian - Petugas')
@section('header-title', 'Form Verifikasi Pengembalian')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header Card -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Verifikasi Pengembalian</h3>
                    <p class="text-sm text-gray-500">Periksa kondisi alat & tentukan denda</p>
                </div>
            </div>
        </div>

        <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST" class="p-6">
            @csrf

            <!-- Info Peminjaman -->
            <div class="mb-5 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Peminjam</p>
                        <p class="font-semibold text-gray-800">{{ $peminjaman->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Rencana Kembali</p>
                        <p class="font-semibold text-gray-800">{{ $peminjaman->tgl_kembali_plan }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-500 text-xs">Alat</p>
                        <p class="font-semibold text-gray-800">
                            @foreach($peminjaman->detailPinjam as $detail)
                                {{ $detail->alat->nama_alat }} ({{ $detail->jumlah }} unit)
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>

            <!-- ==================== INFO DENDA TELAT (OTOMATIS) ==================== -->
            @php
                // Hitung denda telat otomatis
                $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                $hariIni = \Carbon\Carbon::now()->startOfDay();
                $dendaTelat = 0;
                $hariTelat = 0;
                $statusDenda = 'tepat';

                // Kalo hari ini lebih dari tanggal rencana, berarti telat
                if ($hariIni->greaterThan($tglKembaliPlan)) {
                    $hariTelat = $tglKembaliPlan->diffInDays($hariIni);
                    $dendaTelat = $hariTelat * 5000;
                    $statusDenda = 'telat';
                }
            @endphp

            <!-- Tampilkan info denda telat -->
            <div class="mb-5 p-4 rounded-xl border {{ $statusDenda == 'telat' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                <div class="flex items-center gap-3">
                    @if($statusDenda == 'telat')
                        <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        <div>
                            <p class="font-bold text-red-700">Telat {{ $hariTelat }} Hari</p>
                            <p class="text-sm text-red-600">Denda otomatis: Rp {{ number_format($dendaTelat, 0, ',', '.') }}</p>
                        </div>
                    @else
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        <div>
                            <p class="font-bold text-green-700">Tepat Waktu</p>
                            <p class="text-sm text-green-600">Tidak ada denda keterlambatan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ==================== KONDISI KEMBALI ==================== -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clipboard-check text-blue-500 mr-1"></i> Kondisi Kembali
                </label>

                {{-- 
                    onchange="toggleDenda()" 
                    → Fungsi ini dipanggil setiap kali user ganti pilihan dropdown
                    → Nanti ngatur enable/disable field denda tambahan
                --}}
                <select name="kondisi_kembali" id="kondisi_kembali" required onchange="toggleDenda()"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    <option value="baik" {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ old('kondisi_kembali') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="perbaikan" {{ old('kondisi_kembali') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                </select>
            </div>

            <!-- ==================== DENDA TAMBAHAN ==================== -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-money-bill text-blue-500 mr-1"></i> Denda Tambahan
                    <span class="text-xs text-gray-400 font-normal">(Jika Rusak/Perbaikan)</span>
                </label>

                {{-- 
                    Field ini punya id="denda_tambahan"
                    → Nanti di JavaScript, kalo kondisi = "baik", field ini di-disable
                    → Kalo kondisi = "rusak" atau "perbaikan", field ini di-enable
                --}}
                <input type="number" name="denda_tambahan" id="denda_tambahan" 
                    value="{{ old('denda_tambahan', 0) }}" min="0"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400">

                {{-- 
                    Pesan bantuan
                    → Bakal berubah teksnya tergantung kondisi yang dipilih
                --}}
                <p class="text-xs mt-1" id="infoDenda">
                    Pilih kondisi "Rusak" atau "Perbaikan" untuk mengisi denda tambahan
                </p>

                @error('denda_tambahan') 
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- ==================== TOMBOL AKSI ==================== -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('petugas.pengembalian.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-check"></i> Selesai Verifikasi
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // Fungsi toggleDenda()
    // 
    // Fungsi ini bakal dipanggil setiap kali user ganti pilihan
    // di dropdown "Kondisi Kembali"
    //
    // Logika:
    // - Kalo kondisi = "baik" → field denda DISABLE (abu-abu)
    // - Kalo kondisi = "rusak" atau "perbaikan" → field denda ENABLE
    function toggleDenda() {
        // Ambil element dropdown kondisi
        const kondisi = document.getElementById('kondisi_kembali').value;
        
        // Ambil element field denda tambahan
        const dendaField = document.getElementById('denda_tambahan');
        
        // Ambil element teks info
        const infoDenda = document.getElementById('infoDenda');

        // KONDISI 1: Barang "Baik"
        // → Denda tambahan DISABLE (ga bisa diisi)
        if (kondisi === 'baik') {
            dendaField.disabled = true;      // Matiin input
            dendaField.value = 0;            // Set nilai jadi 0
            infoDenda.textContent = 'Kondisi barang baik, tidak ada denda tambahan.';
            infoDenda.className = 'text-xs mt-1 text-green-600'; // Teks ijo
        } 
        // KONDISI 2: Barang "Rusak" atau "Perbaikan"
        // → Denda tambahan ENABLE (bisa diisi)
        else {
            dendaField.disabled = false;     // Hidupin input
            infoDenda.textContent = 'Silakan isi nominal denda sesuai kerusakan.';
            infoDenda.className = 'text-xs mt-1 text-red-600'; // Teks merah
        }
    }

    // Saat halaman pertama kali dibuka,
    // langsung cek kondisi default (biasanya "baik")
    document.addEventListener('DOMContentLoaded', function() {
        toggleDenda();
    });
</script>
@endsection