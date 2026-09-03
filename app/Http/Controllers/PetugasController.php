<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PetugasController extends Controller
{

    // Dashboard Petugas
    public function dashboard()
    {
        $totalPengajuan = Peminjaman::where('status', 'diajukan')->count();
        $totalDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $totalDikembalikan = Peminjaman::where('status', 'dikembalikan')->count();

        return view('petugas.dashboard', compact('totalPengajuan', 'totalDipinjam', 'totalDikembalikan'));
    }

    // Index Peminjaman (yang diajukan)
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('petugas.peminjaman.index', compact('peminjaman', 'search'));
    }

    // Setujui Peminjaman
    public function setujuPeminjaman($id)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

            if ($peminjaman->status != 'diajukan') {
                return back()->with('error', 'Peminjaman sudah diproses.');
            }

            // Cek stok
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                if ($alat->stok < $detail->jumlah) {
                    return back()->with('error', "Stok {$alat->nama_alat} tidak mencukupi.");
                }
            }

            // Kurangi stok
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            $peminjaman->update([
                'status' => 'dipinjam',
                'petugas_id' => auth()->id(),
            ]);

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menyetujui peminjaman #' . $peminjaman->id,
            ]);

            DB::commit();

            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil disetujui! Stok alat dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Index Pengembalian (menunggu verifikasi)
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalian = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'menunggu_verifikasi')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('petugas.pengembalian.index', compact('pengembalian', 'search'));
    }

    // Form verifikasi pengembalian
    public function verifikasiPengembalian($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->findOrFail($id);

        return view('petugas.pengembalian.verifikasi', compact('peminjaman'));
    }

    // Proses verifikasi pengembalian
    public function prosesVerifikasiPengembalian(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak,perbaikan',
            'denda_tambahan' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

            if ($peminjaman->status != 'menunggu_verifikasi') {
                return back()->with('error', 'Peminjaman ini tidak menunggu verifikasi.');
            }

            // Hitung denda telat
            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();

            $dendaTelat = 0;
            if ($hariIni->greaterThan($tglKembaliPlan)) {
                $selisihHari = $tglKembaliPlan->diffInDays($hariIni);
                $dendaTelat = $selisihHari * 5000;
            }

            $dendaTambahan = $request->denda_tambahan ?? 0;
            $totalDenda = $dendaTelat + $dendaTambahan;

            // Simpan pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $totalDenda,
                'petugas_id' => auth()->id(),
            ]);

            // Update status peminjaman
            $statusBaru = $hariIni->greaterThan($tglKembaliPlan) ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            // Kembalikan stok
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Verifikasi pengembalian peminjaman ID: ' . $peminjaman->id,
            ]);

            DB::commit();

            return redirect()->route('petugas.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diverifikasi! Denda: Rp ' . number_format($totalDenda, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Halaman laporan (tampil data + tombol cetak)
    // Laporan dengan filter
    public function laporan(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pinjam', '<=', $request->end_date);
        }

        $peminjaman = $query->latest()->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('petugas.laporan.index', compact('peminjaman'));
    }

    // Cetak Laporan PDF dengan filter
    public function cetakLaporan(Request $request)
    {
        $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pinjam', '<=', $request->end_date);
        }

        $peminjaman = $query->latest()->get();

        $pdf = Pdf::loadView('petugas.laporan.cetak', compact('peminjaman'));
        return $pdf->download('laporan-peminjaman.pdf');
    }

        // Tolak Peminjaman
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            if ($peminjaman->status != 'diajukan') {
                return back()->with('error', 'Peminjaman sudah diproses.');
            }

            $peminjaman->delete();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menolak peminjaman #' . $peminjaman->id,
            ]);

            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman berhasil ditolak dan dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}