@extends('layouts.petugas')

@section('title', 'Verifikasi Pengembalian - Petugas')
@section('header-title', ' Form Verifikasi Pengembalian')

@section('content')
<div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}" method="POST">
        @csrf

        <div class="mb-4 p-4 bg-gray-50 rounded-xl">
            <p><strong>Peminjam:</strong> {{ $peminjaman->user->name }}</p>
            <p><strong>Alat:</strong> 
                @foreach($peminjaman->detailPinjam as $detail)
                    {{ $detail->alat->nama_alat }} ({{ $detail->jumlah }} unit)
                @endforeach
            </p>
            <p><strong>Rencana Kembali:</strong> {{ $peminjaman->tgl_kembali_plan }}</p>
            <p><strong>Status Sekarang:</strong> 
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                    Menunggu Verifikasi
                </span>
            </p>
        </div>

        <!-- ====== INFO DENDA OTOMATIS ====== -->
        @php
            $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $hariIni = \Carbon\Carbon::now()->startOfDay();
            $dendaOtomatis = 0;
            $hariTelat = 0;
            $statusDenda = 'tepat';

            if ($hariIni->greaterThan($tglKembaliPlan)) {
                $hariTelat = $tglKembaliPlan->diffInDays($hariIni);
                $dendaOtomatis = $hariTelat * 5000;
                $statusDenda = 'telat';
            }
        @endphp

        <div class="mb-4 p-4 rounded-xl border {{ $statusDenda == 'telat' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
            <div class="flex items-center gap-3">
                @if($statusDenda == 'telat')
                    <span class="text-2xl">🔴</span>
                    <div>
                        <p class="font-bold text-red-700">Telat {{ $hariTelat }} hari</p>
                        <p class="text-sm text-red-600">Denda otomatis: Rp {{ number_format($dendaOtomatis, 0, ',', '.') }}</p>
                    </div>
                @else
                    <span class="text-2xl">🟢</span>
                    <div>
                        <p class="font-bold text-green-700">Tepat Waktu</p>
                        <p class="text-sm text-green-600">Tidak ada denda keterlambatan</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Kembali</label>
            <select name="kondisi_kembali" required class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="baik">Baik</option>
                <option value="rusak">Rusak</option>
                <option value="perbaikan">Perbaikan</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Denda Tambahan (Jika Rusak/Perbaikan)</label>
            <input type="number" name="denda_tambahan" value="{{ old('denda_tambahan', 0) }}" min="0"
                class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            <p class="text-xs text-gray-400 mt-1">Isi jika kondisi barang rusak atau perlu perbaikan</p>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('petugas.pengembalian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition">Batal</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                <i class="fas fa-check mr-1"></i> Selesai Verifikasi
            </button>
        </div>
    </form>
</div>
@endsection