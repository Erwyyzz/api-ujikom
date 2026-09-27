@extends('layouts.petugas')

@section('title', 'Verifikasi Pengembalian - Petugas')
@section('header-title', 'Form Verifikasi Pengembalian')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Notif validasi error --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm font-semibold text-red-700 mb-2">
                <i class="fas fa-exclamation-circle mr-1"></i> Ada kesalahan:
            </p>
            <ul class="text-xs text-red-600 list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header Card -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Verifikasi Pengembalian</h3>
                    <p class="text-sm text-gray-500">Periksa kondisi tiap alat & tentukan denda</p>
                </div>
            </div>
        </div>

        <form id="formVerifikasi" action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST" class="p-6">
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
                </div>
            </div>

            <!-- ==================== INFO DENDA TELAT (OTOMATIS) ==================== -->
            @php
                $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                $hariIni = \Carbon\Carbon::now()->startOfDay();
                $dendaTelat = 0;
                $hariTelat = 0;
                $statusDenda = 'tepat';

                if ($hariIni->greaterThan($tglKembaliPlan)) {
                    $hariTelat = $tglKembaliPlan->diffInDays($hariIni);
                    $dendaTelat = $hariTelat * 5000;
                    $statusDenda = 'telat';
                }
            @endphp

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

            <!-- ==================== KONDISI ALAT (PER ALAT) ==================== -->
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-tools text-blue-500 mr-1"></i> Kondisi Alat (per alat)
                </label>

                <div class="space-y-3">
                    @foreach($peminjaman->detailPinjam as $index => $detail)
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 alat-row" data-index="{{ $index }}">
                            {{-- Nama Alat + Jumlah --}}
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fas fa-box text-blue-500"></i>
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $detail->alat->nama_alat ?? 'Alat' }}
                                </span>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">
                                    {{ $detail->jumlah }} unit
                                </span>
                            </div>

                            {{-- Kondisi + Denda --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Dropdown Kondisi --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi</label>
                                    <select name="kondisi[{{ $index }}]" required
                                        onchange="toggleDenda({{ $index }})"
                                        id="kondisi_{{ $index }}"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition text-sm bg-white">
                                        <option value="baik" {{ old("kondisi.$index") == 'baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="rusak" {{ old("kondisi.$index") == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                        <option value="perbaikan" {{ old("kondisi.$index") == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                    </select>
                                </div>

                                {{-- Denda Tambahan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                                        Denda Tambahan
                                        <span class="text-gray-400 font-normal">(Rp)</span>
                                    </label>
                                    <input type="number" name="denda_tambahan[{{ $index }}]"
                                        id="denda_{{ $index }}"
                                        value="{{ old("denda_tambahan.$index", 0) }}"
                                        min="0"
                                        oninput="hitungTotal()"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition text-sm disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-400">
                                    <p class="text-xs mt-1 text-gray-500" id="info_{{ $index }}">
                                        Kondisi baik, tidak ada denda tambahan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ==================== TOTAL DENDA ==================== -->
            <div class="mb-5 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Total Denda</p>
                        <p class="text-2xl font-bold text-blue-700" id="totalDendaDisplay">
                            Rp {{ number_format($dendaTelat, 0, ',', '.') }}
                        </p>
                    </div>
                    <i class="fas fa-money-bill-wave text-blue-400 text-3xl"></i>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Denda telat: Rp {{ number_format($dendaTelat, 0, ',', '.') }}
                    + Denda tambahan: <span id="dendaTambahanDisplay">Rp 0</span>
                </p>
            </div>

            <!-- ==================== TOMBOL AKSI ==================== -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('petugas.pengembalian.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="button" id="btnSubmit" onclick="submitVerifikasi()"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-check"></i> Selesai Verifikasi
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    // Denda telat dari server (PHP), dipakai buat hitung total realtime
    const DENDA_TELAT = {{ $dendaTelat }};

    // ============================================================
    // toggleDenda(index)
    // - Kalo kondisi = "baik" → disable input denda, set 0
    // - Kalo kondisi = "rusak" / "perbaikan" → enable input denda
    // ============================================================
    function toggleDenda(index) {
        const kondisi = document.getElementById('kondisi_' + index).value;
        const dendaField = document.getElementById('denda_' + index);
        const infoField = document.getElementById('info_' + index);

        if (kondisi === 'baik') {
            dendaField.disabled = true;
            dendaField.value = 0;
            infoField.textContent = 'Kondisi baik, tidak ada denda tambahan.';
            infoField.className = 'text-xs mt-1 text-green-600';
        } else {
            dendaField.disabled = false;
            infoField.textContent = 'Silakan isi nominal denda sesuai kerusakan.';
            infoField.className = 'text-xs mt-1 text-red-600';
        }

        hitungTotal();
    }

    // ============================================================
    // hitungTotal()
    // - Jumlahin semua input denda_tambahan yang ada
    // - Tambahin denda telat
    // - Update tampilan total denda
    // ============================================================
    function hitungTotal() {
        let totalTambahan = 0;

        document.querySelectorAll('input[name^="denda_tambahan"]').forEach(function(input) {
            if (!input.disabled) {
                totalTambahan += parseInt(input.value) || 0;
            }
        });

        const total = DENDA_TELAT + totalTambahan;

        document.getElementById('dendaTambahanDisplay').textContent =
            'Rp ' + totalTambahan.toLocaleString('id-ID');
        document.getElementById('totalDendaDisplay').textContent =
            'Rp ' + total.toLocaleString('id-ID');
    }

    // ============================================================
    // submitVerifikasi()
    // - Fungsi KHUSUS buat tombol "Selesai Verifikasi"
    // - Manual trigger submit form (ga ngandelin default HTML)
    // - Pastiin dulu semua field enabled biar ke-submit
    // ============================================================
    function submitVerifikasi() {
        // Aktifin semua input denda_tambahan sebelum submit
        // (yang disabled ga akan ke-submit, jadi harus di-enable dulu)
        document.querySelectorAll('input[name^="denda_tambahan"]').forEach(function(input) {
            input.disabled = false;
        });

        // Submit form manual
        document.getElementById('formVerifikasi').submit();
    }

    // ============================================================
    // Init saat halaman pertama kali dibuka
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        const totalAlat = {{ $peminjaman->detailPinjam->count() }};
        for (let i = 0; i < totalAlat; i++) {
            toggleDenda(i);
        }
        hitungTotal();
    });
</script>
@endsection