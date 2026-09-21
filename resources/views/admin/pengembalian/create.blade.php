@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header-title', 'Form Pengembalian Alat')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header Card -->
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-undo-alt text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Form Pengembalian Alat</h3>
                    <p class="text-sm text-gray-500">Proses pengembalian alat dari peminjam</p>
                </div>
            </div>
        </div>

        <!-- Body Form -->
        <form action="{{ route('admin.pengembalian.store') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-list text-blue-500 mr-1"></i> Pilih Peminjaman
                </label>
                <select name="peminjaman_id" id="peminjaman_id" required onchange="updateDendaInfo()"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    <option value="">-- Pilih Peminjaman --</option>
                    @foreach($peminjamans as $peminjaman)
                        <option value="{{ $peminjaman->id }}"
                            data-hari-telat="{{ $peminjaman->hariTelat }}"
                            data-total-denda="{{ $peminjaman->totalDenda }}"
                            data-status-denda="{{ $peminjaman->statusDenda }}">
                            {{ $peminjaman->user->name }} - {{ $peminjaman->tgl_pinjam }} s.d {{ $peminjaman->tgl_kembali_plan }}
                        </option>
                    @endforeach
                </select>
                @error('peminjaman_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Info Denda -->
            <div id="infoDenda" class="mb-5 p-4 rounded-xl border hidden">
                <div id="infoDendaContent"></div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-clipboard-check text-blue-500 mr-1"></i> Kondisi Kembali
                </label>
                <select name="kondisi_kembali" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                    <option value="baik" {{ old('kondisi_kembali') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak" {{ old('kondisi_kembali') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="perbaikan" {{ old('kondisi_kembali') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                </select>
                @error('kondisi_kembali') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-money-bill text-blue-500 mr-1"></i> Denda Tambahan (Jika Rusak/Perbaikan)
                </label>
                <input type="number" name="denda_tambahan" value="{{ old('denda_tambahan', 0) }}" min="0"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                <p class="text-xs text-gray-400 mt-1">Isi jika kondisi barang rusak atau perlu perbaikan</p>
                @error('denda_tambahan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit"
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-save"></i> Proses Pengembalian
                </button>
            </div>

        </form>
    </div>

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
                        <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
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
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
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
</script>
@endsection