@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', '📋 Form Pengembalian Alat')

@section('content')
@if(session('error'))
    <div class="mb-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-exclamation-circle mr-2 text-red-500"></i>{{ session('error') }}</span>
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

<div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.pengembalian.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Peminjaman</label>
            <select name="peminjaman_id" id="peminjaman_id" required 
                class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                onchange="updateDendaInfo()">
                <option value="">-- Pilih Peminjaman --</option>
                @foreach($peminjamans as $peminjaman)
                    <option value="{{ $peminjaman->id }}" 
                        data-hari-telat="{{ $peminjaman->hariTelat }}"
                        data-total-denda="{{ $peminjaman->totalDenda }}"
                        data-status-denda="{{ $peminjaman->statusDenda }}">
                        {{ $peminjaman->user->name }} - {{ $peminjaman->tgl_pinjam }} s.d {{ $peminjaman->tgl_kembali_plan }}
                        ({{ $peminjaman->detailPinjam->sum('jumlah') }} alat)
                    </option>
                @endforeach
            </select>
            @error('peminjaman_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <!-- Info Denda -->
        <div id="infoDenda" class="mb-4 p-4 rounded-xl border hidden">
            <div id="infoDendaContent"></div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Kembali</label>
            <select name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                <option value="baik" {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak" {{ old('kondisi_kembali') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="perbaikan" {{ old('kondisi_kembali') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
            </select>
            @error('kondisi_kembali') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Denda Tambahan (Jika Rusak/Perbaikan)</label>
            <input type="number" name="denda_tambahan" value="{{ old('denda_tambahan', 0) }}" min="0"
                class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            <p class="text-xs text-gray-400 mt-1">Isi jika kondisi barang rusak atau perlu perbaikan</p>
            @error('denda_tambahan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.pengembalian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-save"></i> Proses Pengembalian
            </button>
        </div>
    </form>
</div>

<script>
function updateDendaInfo() {
    const select = document.getElementById('peminjaman_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoDenda = document.getElementById('infoDenda');
    const infoContent = document.getElementById('infoDendaContent');

    if (!selectedOption || !selectedOption.value) {
        infoDenda.classList.add('hidden');
        return;
    }

    const hariTelat = parseInt(selectedOption.dataset.hariTelat) || 0;
    const totalDenda = parseInt(selectedOption.dataset.totalDenda) || 0;
    const statusDenda = selectedOption.dataset.statusDenda || 'tepat';

    let html = '';

    if (statusDenda === 'telat') {
        html = `
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl">🔴</span>
                    <div>
                        <p class="font-bold text-red-700 text-lg">TELAT ${hariTelat} HARI</p>
                        <p class="text-sm text-red-600">Tanggal rencana kembali sudah lewat</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-red-200">
                    <div>
                        <p class="text-xs text-gray-500">Denda per hari</p>
                        <p class="font-semibold text-gray-800">Rp 5.000</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total Denda</p>
                        <p class="font-bold text-red-600 text-lg">Rp ${totalDenda.toLocaleString('id-ID')}</p>
                    </div>
                </div>
            </div>
        `;
    } else {
        html = `
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🟢</span>
                    <div>
                        <p class="font-bold text-green-700 text-lg">TEPAT WAKTU</p>
                        <p class="text-sm text-green-600">Pengembalian sesuai atau sebelum jadwal</p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-green-200">
                    <p class="text-xs text-gray-500">Denda</p>
                    <p class="font-semibold text-green-700">Rp 0 (Tidak ada denda)</p>
                </div>
            </div>
        `;
    }

    infoContent.innerHTML = html;
    infoDenda.classList.remove('hidden');
}

// Jalankan otomatis kalo ada value default
document.addEventListener('DOMContentLoaded', function() {
    updateDendaInfo();
});
</script>
@endsection