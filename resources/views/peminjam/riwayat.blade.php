@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman')
@section('header-title', ' Riwayat Peminjaman Saya')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-history text-blue-500"></i> Riwayat Peminjaman Saya
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $riwayat->count() }}</span>
        </h3>
        <p class="text-sm text-gray-400 mt-1">Status peminjaman Anda dari waktu ke waktu</p>
    </div>

    @if($riwayat->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Alat</th>
                    <th class="py-3.5 px-4">Jumlah</th>
                    <th class="py-3.5 px-4">Tgl Pinjam</th>
                    <th class="py-3.5 px-4">Rencana Kembali</th>
                    <th class="py-3.5 px-4">Tgl Kembali</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4">Denda</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @foreach($riwayat as $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? 'Alat' }}<br>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->jumlah }}<br>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">{{ $item->tgl_pinjam }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_kembali_plan ?? '-' }}</td>
                    <td class="py-3 px-4">
                        @if($item->pengembalian)
                            {{ $item->pengembalian->tgl_kembali }}
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @php
                            $statusClass = match($item->status) {
                                'diajukan' => 'bg-yellow-100 text-yellow-700',
                                'dipinjam' => 'bg-blue-100 text-blue-700',
                                'menunggu_verifikasi' => 'bg-purple-100 text-purple-700',
                                'dikembalikan' => 'bg-green-100 text-green-700',
                                'telat' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $statusLabel = match($item->status) {
                                'diajukan' => '🟡 Menunggu Persetujuan',
                                'dipinjam' => '🔵 Sedang Dipinjam',
                                'menunggu_verifikasi' => '🟣 Menunggu Verifikasi',
                                'dikembalikan' => '🟢 Selesai Dikembalikan',
                                'telat' => '🔴 Telat',
                                default => ucfirst($item->status),
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-semibold text-red-600">
                        @if($item->pengembalian && $item->pengembalian->denda > 0)
                            Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-10 text-center text-gray-400">
        <i class="fas fa-inbox text-4xl block mb-2 text-gray-300"></i>
        Belum ada riwayat peminjaman.
    </div>
    @endif

    <!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $riwayat->links() }}
</div>
</div>
@endsection