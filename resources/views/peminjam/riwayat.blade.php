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
                    <td class="py-3 px-4 text-right whitespace-nowrap">
                        @if($item->pengembalian && $item->pengembalian->denda > 0)
                            <span class="font-semibold text-red-600 mr-2">
                                Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-gray-400 mr-2">-</span>
                        @endif

                        @if(in_array($item->status, ['dikembalikan', 'telat']))
                            <button type="button"
                                onclick="showInfoModal({{ $item->id }})"
                                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded-lg font-semibold transition inline-flex items-center gap-1 align-middle">
                                <i class="fas fa-info-circle"></i> Info
                            </button>
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

{{-- MODAL INFO RINCIAN DENDA (per-alat)                          --}}
<div id="modalInfo" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto animate-fadeIn">

        {{-- Header --}}
        <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800" id="modalTitle">Rincian Peminjaman</h3>
                        <p class="text-xs text-gray-500" id="modalSubtitle">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeInfoModal()"
                    class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full hover:bg-white flex items-center justify-center transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-5">

            {{-- Info Peminjaman --}}
            <div class="mb-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <p class="text-gray-500">Tgl Pinjam</p>
                        <p class="font-semibold text-gray-800" id="modalTglPinjam">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Rencana Kembali</p>
                        <p class="font-semibold text-gray-800" id="modalTglPlan">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tgl Kembali</p>
                        <p class="font-semibold text-gray-800" id="modalTglKembali">-</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="font-semibold text-gray-800" id="modalStatus">-</p>
                    </div>
                </div>
            </div>

            {{-- Kondisi Alat --}}
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                    <i class="fas fa-tools text-blue-500 mr-1"></i> Kondisi Alat
                </p>
                <div class="space-y-2" id="modalAlatList">
                    {{-- Diisi via JavaScript --}}
                </div>
            </div>

            {{-- Ringkasan Denda --}}
            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">
                    <i class="fas fa-money-bill-wave text-blue-500 mr-1"></i> Ringkasan Denda
                </p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-700">
                        <span>Denda Telat</span>
                        <span id="modalDendaTelat">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Denda Kerusakan</span>
                        <span id="modalDendaRusak">Rp 0</span>
                    </div>
                    <div class="flex justify-between pt-3 mt-2 border-t-2 border-blue-200 text-base">
                        <span class="font-bold text-gray-800">TOTAL</span>
                        <span class="font-bold text-blue-700" id="modalDendaTotal">Rp 0</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <button type="button" onclick="closeInfoModal()"
                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                Tutup
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
    // Data riwayat dari server, di-embed ke JavaScript
    // Format: { id_peminjaman: { ...data... }, ... }
    const RIWAYAT_DATA = {
        @foreach($riwayat as $item)
            {{ $item->id }}: {
                id: {{ $item->id }},
                tgl_pinjam: "{{ \Carbon\Carbon::parse($item->tgl_pinjam)->translatedFormat('d F Y') }}",
                tgl_kembali_plan: "{{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->translatedFormat('d F Y') }}",
                tgl_kembali: "{{ $item->pengembalian ? \Carbon\Carbon::parse($item->pengembalian->tgl_kembali)->translatedFormat('d F Y') : '-' }}",
                status: "{{ $item->status }}",
                denda_total: {{ $item->pengembalian->denda ?? 0 }},
                alat: [
                    @foreach($item->detailPinjam as $detail)
                        {
                            nama: "{{ $detail->alat->nama_alat ?? 'Alat' }}",
                            jumlah: {{ $detail->jumlah }},
                            kondisi: "{{ $detail->kondisi_kembali ?? 'baik' }}",
                            denda: {{ $detail->denda_tambahan ?? 0 }}
                        },
                    @endforeach
                ]
            },
        @endforeach
    };

    // showInfoModal(id)
    // - Nampilin modal popup dengan detail peminjaman
    // - Isi data dari RIWAYAT_DATA
    function showInfoModal(id) {
        const data = RIWAYAT_DATA[id];
        if (!data) return;

        // Update header
        document.getElementById('modalTitle').textContent = 'Rincian Peminjaman #' + data.id;
        document.getElementById('modalSubtitle').textContent =
            (data.status === 'telat' ? 'Telat' : 'Dikembalikan') + ' • ' + data.tgl_kembali;

        // Update info peminjaman
        document.getElementById('modalTglPinjam').textContent = data.tgl_pinjam;
        document.getElementById('modalTglPlan').textContent = data.tgl_kembali_plan;
        document.getElementById('modalTglKembali').textContent = data.tgl_kembali;

        // Status label
        const statusLabels = {
            'dikembalikan': '🟢 Selesai',
            'telat': '🔴 Telat'
        };
        document.getElementById('modalStatus').textContent = statusLabels[data.status] || data.status;

        // ===== Alat list =====
        const alatList = document.getElementById('modalAlatList');
        alatList.innerHTML = '';

        let totalDendaRusak = 0;

        data.alat.forEach(function(alat) {
            totalDendaRusak += alat.denda;

            // Tentukan style kondisi
            let kondisiIcon, kondisiColor;
            if (alat.kondisi === 'baik') {
                kondisiIcon = '🟢';
                kondisiColor = 'text-green-600';
            } else if (alat.kondisi === 'rusak') {
                kondisiIcon = '🔴';
                kondisiColor = 'text-red-600';
            } else {
                kondisiIcon = '🟡';
                kondisiColor = 'text-yellow-600';
            }

            const dendaText = alat.denda > 0
                ? 'Rp ' + alat.denda.toLocaleString('id-ID')
                : '-';

            const div = document.createElement('div');
            div.className = 'p-3 bg-gray-50 rounded-xl border border-gray-100';
            div.innerHTML = `
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">${alat.nama}</p>
                        <p class="text-xs text-gray-500">${alat.jumlah} unit</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="${kondisiColor} font-semibold">
                        ${kondisiIcon} ${alat.kondisi.charAt(0).toUpperCase() + alat.kondisi.slice(1)}
                    </span>
                    <span class="font-semibold text-gray-700">${dendaText}</span>
                </div>
            `;
            alatList.appendChild(div);
        });

        // ===== Ringkasan denda =====
        // Denda telat = total denda - denda kerusakan
        const dendaTelat = Math.max(0, data.denda_total - totalDendaRusak);

        document.getElementById('modalDendaTelat').textContent =
            'Rp ' + dendaTelat.toLocaleString('id-ID');
        document.getElementById('modalDendaRusak').textContent =
            'Rp ' + totalDendaRusak.toLocaleString('id-ID');
        document.getElementById('modalDendaTotal').textContent =
            'Rp ' + data.denda_total.toLocaleString('id-ID');

        // ===== Show modal =====
        document.getElementById('modalInfo').classList.remove('hidden');
        document.getElementById('modalInfo').classList.add('flex');
    }

    // closeInfoModal()
    function closeInfoModal() {
        document.getElementById('modalInfo').classList.add('hidden');
        document.getElementById('modalInfo').classList.remove('flex');
    }

    // Close modal kalau klik backdrop
    document.getElementById('modalInfo').addEventListener('click', function(e) {
        if (e.target === this) {
            closeInfoModal();
        }
    });
</script>
@endsection