@extends('layouts.peminjam')

@section('title', 'Pengembalian Alat')
@section('header-title', ' Daftar Alat yang Dipinjam')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 p-4 rounded-2xl shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-undo-alt text-blue-500"></i> Alat yang Sedang Dipinjam
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $peminjaman->count() }}</span>
        </h3>
        <p class="text-sm text-gray-400 mt-1">Klik tombol "Kembalikan" untuk mengajukan pengembalian alat</p>
    </div>

    @if($peminjaman->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Alat</th>
                    <th class="py-3.5 px-4">Jumlah</th>
                    <th class="py-3.5 px-4">Tgl Pinjam</th>
                    <th class="py-3.5 px-4">Rencana Kembali</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @foreach($peminjaman as $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->alat->nama_alat ?? 'Alat' }}
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            {{ $detail->jumlah }}
                        @endforeach
                    </td>
                    <td class="py-3 px-4">{{ $item->tgl_pinjam }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_kembali_plan ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" 
                                onclick="openKembalikanModal({{ $item->id }}, '{{ $item->detailPinjam->first()->alat->nama_alat ?? 'Alat' }}')" 
                                class="bg-[#E07A5F] hover:bg-[#D96A4F] text-white px-4 py-2 rounded-lg text-xs font-semibold transition inline-block">
                            <i class="fas fa-undo mr-1"></i> Kembalikan
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-10 text-center text-gray-400">
        <i class="fas fa-check-circle text-4xl block mb-2 text-green-300"></i>
        Tidak ada alat yang sedang dipinjam.
    </div>
    @endif
</div>

<!-- ====== MODAL KONFIRMASI KEMBALIKAN ====== -->
<div id="modalKembalikan" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 animate-fadeIn">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-undo-alt text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Konfirmasi Pengembalian</h3>
            <p class="text-sm text-gray-500 mt-2" id="modalPesan">Apakah Anda yakin ingin mengembalikan alat ini?</p>
            <p class="text-sm text-gray-500 font-semibold mt-1" id="modalNamaAlat"></p>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closeKembalikanModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                Batal
            </button>
            <form id="formKembalikan" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                    Ya, Kembalikan
                </button>
            </form>
        </div>
    </div>

    <!-- Pagination -->
<div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-center">
    {{ $peminjaman->links() }}
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
    function openKembalikanModal(id, namaAlat) {
        document.getElementById('modalPesan').textContent = 'Apakah Anda yakin ingin mengembalikan alat ini?';
        document.getElementById('modalNamaAlat').textContent = '📦 ' + namaAlat;
        document.getElementById('formKembalikan').action = '/peminjam/kembalikan/' + id;
        document.getElementById('modalKembalikan').classList.remove('hidden');
        document.getElementById('modalKembalikan').classList.add('flex');
    }

    function closeKembalikanModal() {
        document.getElementById('modalKembalikan').classList.add('hidden');
        document.getElementById('modalKembalikan').classList.remove('flex');
    }
</script>
@endsection