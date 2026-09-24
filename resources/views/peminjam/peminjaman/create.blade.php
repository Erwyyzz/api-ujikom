@extends('layouts.peminjam')

@section('title', 'Ajukan Peminjaman')
@section('header-title', 'Form Ajukan Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto">

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
                    <p class="text-sm text-gray-500">Bisa pilih lebih dari 1 alat</p>
                </div>
            </div>
        </div>

        {{-- Body Form --}}
        <form id="formPeminjaman" action="{{ route('peminjam.peminjaman.store') }}" method="POST" class="p-6">
            @csrf

            {{-- Container Alat --}}
            <div id="alatContainer" class="space-y-5 mb-5">

                {{-- Alat #1 (Template) --}}
                <div class="alat-item bg-gray-50 rounded-2xl border border-gray-200 p-5" data-index="0">
                    
                    {{-- Dropdown Pilih Alat --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-toolbox text-blue-500 mr-1"></i> Pilih Alat
                        </label>
                        <select name="alat_id[]" class="alat-select w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition bg-white" required onchange="pilihAlat(this)">
                            <option value="">-- Pilih Alat --</option>
                            @foreach($alats as $item)
                                <option value="{{ $item->id }}" 
                                    data-nama="{{ $item->nama_alat }}"
                                    data-kategori="{{ $item->kategori->nama_kategori ?? 'Umum' }}"
                                    data-stok="{{ $item->stok }}"
                                    data-kondisi="{{ $item->kondisi }}"
                                    data-gambar="{{ $item->gambar ? asset('uploads/alats/' . $item->gambar) : '' }}"
                                    {{ $alat && $alat->id == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_alat }} (Stok: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Info Alat (Muncul kalo alat udah dipilih) --}}
                    <div class="info-alat hidden mb-4 p-4 bg-white rounded-xl border border-gray-200">
                        <div class="flex items-start gap-4">
                            {{-- Gambar --}}
                            <img src="" alt="Alat" class="info-gambar w-20 h-20 object-cover rounded-xl border border-gray-200">
                            
                            {{-- Info --}}
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 info-nama">-</h4>
                                <p class="text-xs text-gray-500 mb-1">
                                    <i class="fas fa-tag text-blue-500 mr-1"></i>
                                    <span class="info-kategori">-</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                    Kondisi: 
                                    <span class="font-semibold info-kondisi">-</span>
                                </p>
                            </div>
                            
                            {{-- Stok --}}
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Stok</p>
                                <p class="text-xl font-bold info-stok">-</p>
                            </div>
                        </div>
                    </div>

                    {{-- Jumlah & Keterangan --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Jumlah --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sort-numeric-up text-blue-500 mr-1"></i> Jumlah
                            </label>
                            <input type="number" name="jumlah[]" class="jumlah-input w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition bg-white" min="1" required placeholder="Contoh: 2">
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sticky-note text-blue-500 mr-1"></i> Keterangan (Opsional)
                            </label>
                            <input type="text" name="keterangan[]" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition bg-white" placeholder="Contoh: Untuk praktikum">
                        </div>
                    </div>

                    {{-- Info Stok Realtime --}}
                    <div class="info-stok-realtime hidden mt-3 p-3 rounded-xl border"></div>

                    {{-- Tombol Hapus (kalo lebih dari 1 alat) --}}
                    <button type="button" class="btn-hapus-alat hidden mt-4 bg-red-100 hover:bg-red-200 text-red-600 px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-2" onclick="hapusAlat(this)">
                        <i class="fas fa-trash-alt"></i> Hapus Alat
                    </button>
                </div>
            </div>

            {{-- Tombol Tambah Alat --}}
            <div class="text-center mb-5">
                <button type="button" onclick="tambahAlat()" 
                    class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-6 py-3 rounded-xl text-sm font-semibold transition inline-flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Tambah Alat
                </button>
            </div>

            {{-- Tanggal Rencana Kembali (Cuma 1 buat semua alat) --}}
            <div class="pt-5 border-t border-gray-100 mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Rencana Kembali
                </label>
                <input type="date" name="tgl_kembali_plan" 
                    id="tgl_kembali_plan"
                    value="{{ old('tgl_kembali_plan', date('Y-m-d', strtotime('+1 day'))) }}" 
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Tanggal ini berlaku untuk semua alat
                </p>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('peminjam.dashboard') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="button" id="btnAjukan" onclick="showKonfirmasiModal()"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI --}}
<div id="modalKonfirmasi" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-lg w-full mx-4 animate-fadeIn max-h-[90vh] overflow-y-auto">
        <div class="text-center mb-5">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-clipboard-check text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Konfirmasi Peminjaman</h3>
            <p class="text-xs text-gray-500 mt-1">Berikut adalah alat yang akan dipinjam</p>
        </div>

        {{-- List Alat --}}
        <div id="popupListAlat" class="space-y-2 mb-4"></div>

        {{-- Tanggal --}}
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 mb-4">
            <p class="text-xs text-gray-500">Tanggal Rencana Kembali</p>
            <p class="font-semibold text-gray-800" id="popupTanggal">-</p>
        </div>

        <p class="text-sm text-gray-500 text-center mb-4">
            Apakah Anda yakin ingin mengajukan peminjaman ini?
        </p>

        <div class="flex gap-3">
            <button type="button" onclick="closeKonfirmasiModal()" 
                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                Batal
            </button>
            <button type="button" onclick="submitForm()" 
                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200">
                Ya, Ajukan
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }
</style>

<script>
    // ============================================================
    // FUNGSI PILIH ALAT (Update info alat + validasi stok)
    // ============================================================
    function pilihAlat(select) {
        const item = select.closest('.alat-item');
        const selectedOption = select.options[select.selectedIndex];
        const infoAlat = item.querySelector('.info-alat');
        const jumlahInput = item.querySelector('.jumlah-input');

        // Kalo ga ada yang dipilih, sembunyikan info
        if (!selectedOption.value) {
            infoAlat.classList.add('hidden');
            return;
        }

        // Ambil data dari option
        const nama = selectedOption.dataset.nama;
        const kategori = selectedOption.dataset.kategori;
        const stok = parseInt(selectedOption.dataset.stok) || 0;
        const kondisi = selectedOption.dataset.kondisi;
        const gambar = selectedOption.dataset.gambar;

        // Update info alat
        infoAlat.querySelector('.info-nama').textContent = nama;
        infoAlat.querySelector('.info-kategori').textContent = kategori;
        infoAlat.querySelector('.info-stok').textContent = stok;
        
        // Update gambar
        const imgEl = infoAlat.querySelector('.info-gambar');
        if (gambar) {
            imgEl.src = gambar;
            imgEl.style.display = 'block';
        } else {
            imgEl.style.display = 'none';
        }

        // Update kondisi dengan warna
        const kondisiEl = infoAlat.querySelector('.info-kondisi');
        kondisiEl.textContent = kondisi.charAt(0).toUpperCase() + kondisi.slice(1);
        kondisiEl.className = 'font-semibold info-kondisi';
        if (kondisi === 'baik') kondisiEl.classList.add('text-green-600');
        else if (kondisi === 'rusak') kondisiEl.classList.add('text-red-600');
        else kondisiEl.classList.add('text-yellow-600');

        // Set max jumlah
        jumlahInput.max = stok;
        jumlahInput.dataset.stok = stok;

        // Munculin info alat
        infoAlat.classList.remove('hidden');

        // Cek stok realtime
        cekStokRealtime(item);
    }

    // ============================================================
    // FUNGSI CEK STOK REALTIME
    // ============================================================
    function cekStokRealtime(item) {
        const jumlahInput = item.querySelector('.jumlah-input');
        const infoStok = item.querySelector('.info-stok-realtime');
        const stok = parseInt(jumlahInput.dataset.stok) || 0;
        const jumlah = parseInt(jumlahInput.value) || 0;

        if (!jumlah || !stok) {
            infoStok.classList.add('hidden');
            return;
        }

        if (jumlah > stok) {
            infoStok.innerHTML = `
                <p class="text-xs text-red-600 font-semibold">
                    <i class="fas fa-times-circle mr-1"></i>
                    Stok tidak cukup! Tersedia: ${stok} unit
                </p>
            `;
            infoStok.className = 'info-stok-realtime mt-3 p-3 rounded-xl border bg-red-50 border-red-200';
        } else {
            infoStok.innerHTML = `
                <p class="text-xs text-green-600 font-semibold">
                    <i class="fas fa-check-circle mr-1"></i>
                    Stok mencukupi (${stok} unit tersedia)
                </p>
            `;
            infoStok.className = 'info-stok-realtime mt-3 p-3 rounded-xl border bg-green-50 border-green-200';
        }
        infoStok.classList.remove('hidden');
    }

    // ============================================================
    // FUNGSI TAMBAH ALAT
    // ============================================================
    function tambahAlat() {
        const container = document.getElementById('alatContainer');
        const items = container.querySelectorAll('.alat-item');
        
        // Clone item pertama
        const newItem = items[0].cloneNode(true);
        
        // Reset value
        newItem.querySelector('.alat-select').value = '';
        newItem.querySelector('.jumlah-input').value = '';
        newItem.querySelectorAll('input[type="text"]').forEach(el => el.value = '');
        newItem.querySelector('.info-alat').classList.add('hidden');
        newItem.querySelector('.info-stok-realtime').classList.add('hidden');
        
        // Munculin tombol hapus
        newItem.querySelector('.btn-hapus-alat').classList.remove('hidden');
        
        // Tambahin ke container
        container.appendChild(newItem);
        
        // Update tombol hapus di semua item
        updateTombolHapus();
    }

    // ============================================================
    // FUNGSI HAPUS ALAT
    // ============================================================
    function hapusAlat(btn) {
        const item = btn.closest('.alat-item');
        item.remove();
        updateTombolHapus();
    }

    // ============================================================
    // UPDATE TOMBOL HAPUS (Kalo cuma 1 alat, sembunyikan)
    // ============================================================
    function updateTombolHapus() {
        const items = document.querySelectorAll('.alat-item');
        const btnHapusList = document.querySelectorAll('.btn-hapus-alat');
        
        if (items.length > 1) {
            // Munculin semua tombol hapus
            btnHapusList.forEach(btn => btn.classList.remove('hidden'));
        } else {
            // Sembunyikan kalo cuma 1
            btnHapusList.forEach(btn => btn.classList.add('hidden'));
        }
    }

    // ============================================================
    // MODAL KONFIRMASI
    // ============================================================
    function showKonfirmasiModal() {
        // Ambil semua alat yang dipilih
        const items = document.querySelectorAll('.alat-item');
        const listAlat = document.getElementById('popupListAlat');
        listAlat.innerHTML = '';

        let validCount = 0;

        items.forEach((item, index) => {
            const select = item.querySelector('.alat-select');
            const jumlah = item.querySelector('.jumlah-input').value;
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                validCount++;
                const nama = selectedOption.dataset.nama;
                
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100';
                div.innerHTML = `
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-xs font-bold">
                        ${validCount}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">${nama}</p>
                        <p class="text-xs text-gray-500">${jumlah} unit</p>
                    </div>
                `;
                listAlat.appendChild(div);
            }
        });

        // Tanggal
        const tanggal = document.getElementById('tgl_kembali_plan').value;
        const tanggalFormat = new Date(tanggal).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        document.getElementById('popupTanggal').textContent = tanggalFormat;

        // Munculin modal
        document.getElementById('modalKonfirmasi').classList.remove('hidden');
        document.getElementById('modalKonfirmasi').classList.add('flex');
    }

    function closeKonfirmasiModal() {
        document.getElementById('modalKonfirmasi').classList.add('hidden');
        document.getElementById('modalKonfirmasi').classList.remove('flex');
    }

    function submitForm() {
        document.getElementById('formPeminjaman').submit();
    }

    // ============================================================
    // INIT: Kalo ada alat yang udah kepilih dari URL
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.alat-select');
        selects.forEach(select => {
            if (select.value) {
                pilihAlat(select);
            }
        });

        // Event listener jumlah input
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('jumlah-input')) {
                const item = e.target.closest('.alat-item');
                cekStokRealtime(item);
            }
        });

        updateTombolHapus();
    });
</script>
@endsection