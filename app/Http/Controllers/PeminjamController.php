<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Pengembalian;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamController extends Controller
{
    // Katalog Alat
    public function katalogAlat()
    {
        $alats = Alat::with('kategori')->where('stok', '>', 0)->get();
        return view('peminjam.dashboard', compact('alats'));
    }

    // Form Ajukan Peminjaman
    public function createPeminjaman($alat_id = null)
    {
        // Ambil data alat yang dipilih dari katalog
        $alat = $alat_id ? Alat::with('kategori')->findOrFail($alat_id) : null;
        
        // Kalo alat ga ada, redirect balik ke dashboard
        if (!$alat) {
            return redirect()->route('peminjam.dashboard')
                ->with('error', 'Silakan pilih alat terlebih dahulu dari katalog.');
        }
        
        return view('peminjam.peminjaman.create', compact('alat'));
    }

    // Store - Ajukan Peminjaman
    public function storePeminjaman(Request $request)
    {
        // Validasi input
        $request->validate([
            'alat_id' => 'required|exists:alat,id',
            'jumlah' => 'required|integer|min:1',
            'tgl_kembali_plan' => 'required|date|after:today',
        ]);

        DB::beginTransaction();

        try {
            // Ambil data alat
            $alat = Alat::findOrFail($request->alat_id);

            // ==========================================
            // VALIDASI STOK: Kalo jumlah pinjam > stok
            // → Kembalikan ke form + error
            // ==========================================
            if ($alat->stok < $request->jumlah) {
                // Kembalikan ke halaman sebelumnya + pesan error
                return back()
                    ->withInput() // Biar input sebelumnya ga ilang
                    ->with('error', 'Stok alat "' . $alat->nama_alat . '" tidak mencukupi! Stok tersedia: ' . $alat->stok . ' unit, Anda minta: ' . $request->jumlah . ' unit.');
            }

            // Kalo stok cukup, lanjut buat peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Simpan detail peminjaman
            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $request->alat_id,
                'jumlah' => $request->jumlah,
            ]);

            // Catat log aktivitas
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Mengajukan peminjaman alat: ' . $alat->nama_alat . ' (' . $request->jumlah . ' unit)',
            ]);

            DB::commit();

            return redirect()->route('peminjam.riwayat')
                ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan petugas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Riwayat Peminjaman (SEMUA status)
    public function riwayatPeminjaman()
    {
        $riwayat = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('peminjam.riwayat', compact('riwayat'));
    }

    // Form Pengembalian
    public function createPengembalian($peminjaman_id)
    {
        $peminjaman = Peminjaman::with(['detailPinjam.alat', 'user'])
            ->where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->findOrFail($peminjaman_id);

        return view('peminjam.pengembalian.create', compact('peminjaman'));
    }

    // Store - Proses Pengembalian
    public function storePengembalian(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'kondisi_kembali' => 'required|in:baik,rusak,perbaikan',
            'denda_tambahan' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($request->peminjaman_id);

            if ($peminjaman->status != 'dipinjam') {
                return back()->with('error', 'Peminjaman ini tidak sedang dipinjam.');
            }

            if ($peminjaman->user_id != auth()->id()) {
                return back()->with('error', 'Anda tidak memiliki akses ke peminjaman ini.');
            }

            // Hitung denda
            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglKembaliAktual = Carbon::now()->startOfDay();

            $dendaTelat = 0;
            if ($tglKembaliAktual->greaterThan($tglKembaliPlan)) {
                $selisihHari = $tglKembaliPlan->diffInDays($tglKembaliAktual);
                $dendaTelat = $selisihHari * 5000;
            }

            $dendaTambahan = $request->denda_tambahan ?? 0;
            $totalDenda = $dendaTelat + $dendaTambahan;

            // Simpan pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $totalDenda,
                'petugas_id' => auth()->id(),
            ]);

            // Update status peminjaman
            $statusBaru = $tglKembaliAktual->greaterThan($tglKembaliPlan) ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            // Kembalikan stok
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Mengembalikan alat peminjaman ID: ' . $peminjaman->id,
            ]);

            DB::commit();

            return redirect()->route('peminjam.riwayat')
                ->with('success', 'Pengembalian berhasil! Denda: Rp ' . number_format($totalDenda, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    //dashboarddddddddddddddddddddd
    public function dashboard()
    {
        // ===== STATISTIK =====
        $totalDiajukan = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'diajukan')
            ->count();

        $totalDipinjam = Peminjaman::where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->count();

        $totalDikembalikan = Peminjaman::where('user_id', auth()->id())
            ->whereIn('status', ['dikembalikan', 'telat'])
            ->count();

        // ===== RIWAYAT AKTIF =====
        $riwayat = Peminjaman::with(['detailPinjam.alat'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['diajukan', 'dipinjam', 'menunggu_verifikasi'])
            ->latest()
            ->get();

        // ===== KATALOG ALAT =====
        // Ambil SEMUA alat (termasuk yang stok habis)
        $alats = Alat::with('kategori')->latest()->get();

        // ===== PERINGATAN PENGEMBALIAN =====
        $peminjamanAktif = Peminjaman::with(['detailPinjam.alat'])
            ->where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->get();

        $peringatan = [];
        foreach ($peminjamanAktif as $item) {
            $tglKembaliPlan = Carbon::parse($item->tgl_kembali_plan)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();
            $selisihHari = $hariIni->diffInDays($tglKembaliPlan, false);

            $peringatan[] = [
                'id' => $item->id,
                'nama_alat' => $item->detailPinjam->first()->alat->nama_alat ?? 'Alat',
                'tgl_kembali_plan' => $item->tgl_kembali_plan,
                'selisih_hari' => $selisihHari,
                'status' => $selisihHari < 0 ? 'telat' : ($selisihHari <= 3 ? 'segera' : 'aman'),
            ];
        }

        $peringatan = array_filter($peringatan, function($p) {
            return $p['status'] != 'aman';
        });

        return view('peminjam.dashboard', compact(
            'totalDiajukan', 'totalDipinjam', 'totalDikembalikan',
            'riwayat', 'alats', 'peringatan'
        ));
    }

    // Index Pengembalian - Menampilkan alat yang sedang dipinjam
    public function indexPengembalian()
    {
        $peminjaman = Peminjaman::with(['detailPinjam.alat'])
            ->where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('peminjam.pengembalian.index', compact('peminjaman'));
    }

    // Proses kembalikan alat (update status jadi menunggu_verifikasi)
    public function kembalikan($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Validasi: cek apakah peminjaman milik user yang login
            if ($peminjaman->user_id != auth()->id()) {
                return back()->with('error', 'Anda tidak memiliki akses ke peminjaman ini.');
            }

            // Validasi: cek apakah status 'dipinjam'
            if ($peminjaman->status != 'dipinjam') {
                return back()->with('error', 'Peminjaman ini tidak sedang dipinjam.');
            }

            // Update status ke 'menunggu_verifikasi'
            $peminjaman->update([
                'status' => 'menunggu_verifikasi',
            ]);

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Mengajukan pengembalian alat (Peminjaman ID: ' . $peminjaman->id . ')',
            ]);

            return redirect()->route('peminjam.dashboard')
                ->with('success', 'Pengembalian berhasil diajukan! Menunggu verifikasi petugas.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}