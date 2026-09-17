@extends('layouts.petugas')

@section('title', 'Persetujuan Peminjaman - Petugas')
@section('header-title', ' Daftar Pengajuan Peminjaman Alat')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 text-emerald-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2 text-emerald-500"></i>{{ session('success') }}</span>
        <button type="button" class="text-emerald-600 hover:text-emerald-800" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 text-red-800 p-4 rounded-2xl shadow-sm text-sm flex items-center justify-between">
        <span><i class="fas fa-exclamation-circle mr-2 text-red-500"></i>{{ session('error') }}</span>
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-hand-holding text-blue-500"></i> Daftar Pengajuan Peminjaman Alat
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $peminjaman->count() }}</span>
            </h3>
            <p class="text-sm text-gray-400 mt-1">Menunggu Verifikasi Persetujuan</p>
        </div>
        
        <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="flex w-full md:w-64">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Cari nama peminjam..." 
                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm font-semibold rounded-r-xl transition">
                <i class="fas fa-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('petugas.peminjaman.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-600 px-3 py-2 text-sm rounded-xl flex items-center transition">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/80 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Peminjam</th>
                    <th class="py-3.5 px-4">Tgl Pinjam</th>
                    <th class="py-3.5 px-4">Rencana Kembali</th>
                    <th class="py-3.5 px-4">Detail Alat</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                @forelse($peminjaman as $item)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="py-3 px-4 font-medium text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $item->user->name ?? '-' }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_pinjam }}</td>
                    <td class="py-3 px-4">{{ $item->tgl_kembali_plan ?? '-' }}</td>
                    <td class="py-3 px-4">
                        @foreach($item->detailPinjam as $detail)
                            <span class="inline-block bg-gray-100 text-gray-700 px-2 py-0.5 rounded-lg text-xs mr-1 mb-1">
                                {{ $detail->alat->nama_alat ?? 'Alat' }}
                                <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-md text-[10px]">Jumlah: {{ $detail->jumlah }}</span>
                            </span>
                        @endforeach
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" 
                                    onclick="openModal({{ $item->id }}, '{{ $item->user->name }}', 'setuju')" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button type="button" 
                                    onclick="openModal({{ $item->id }}, '{{ $item->user->name }}', 'tolak')" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl block mb-2 text-gray-300"></i>
                        Tidak ada pengajuan peminjaman.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ====== MODAL KONFIRMASI SETUJU/TOLAK ====== -->
<div id="modalKonfirmasi" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 animate-fadeIn">
        <div class="text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" id="modalIcon">
                <i class="fas fa-check-circle text-2xl" id="modalIconText"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Konfirmasi</h3>
            <p class="text-sm text-gray-500 mt-2" id="modalMessage">Apakah Anda yakin?</p>
            <p class="text-sm text-gray-500 font-semibold mt-1" id="modalNamaPeminjam"></p>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closeModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition">
                Batal
            </button>
            <form id="formAction" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition" id="btnAction">
                    Ya
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
    function openModal(id, nama, action) {
        const modal = document.getElementById('modalKonfirmasi');
        const icon = document.getElementById('modalIcon');
        const iconText = document.getElementById('modalIconText');
        const title = document.getElementById('modalTitle');
        const message = document.getElementById('modalMessage');
        const namaPeminjam = document.getElementById('modalNamaPeminjam');
        const btn = document.getElementById('btnAction');
        const form = document.getElementById('formAction');

        if (action === 'setuju') {
            icon.className = 'w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4';
            iconText.className = 'fas fa-check-circle text-green-600 text-2xl';
            title.textContent = 'Setujui Peminjaman';
            message.textContent = 'Apakah Anda yakin ingin menyetujui peminjaman ini?';
            btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition';
            btn.textContent = 'Ya, Setujui';
            form.action = '/petugas/peminjaman/' + id + '/setuju';
        } else {
            icon.className = 'w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4';
            iconText.className = 'fas fa-times-circle text-red-600 text-2xl';
            title.textContent = 'Tolak Peminjaman';
            message.textContent = 'Apakah Anda yakin ingin menolak peminjaman ini?';
            btn.className = 'w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition';
            btn.textContent = 'Ya, Tolak';
            form.action = '/petugas/peminjaman/' + id + '/tolak';
        }

        namaPeminjam.textContent = '👤 ' + nama;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('modalKonfirmasi');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

@endsection