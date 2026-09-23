<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    // ==================== CRUD ALAT ====================

    // CRUD Alat: Menampilkan daftar alat dengan search + pagination
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', '%' . $search . '%')
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    // menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    // menyimpan data alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama_alat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:baik,rusak,perbaikan',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/alats'), $filename);
            $data['gambar'] = $filename;
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil ditambahkan.');
    }

    // menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama_alat' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:baik,rusak,perbaikan',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar && file_exists(public_path('uploads/alats/' . $alat->gambar))) {
                unlink(public_path('uploads/alats/' . $alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/alats'), $filename);
            $data['gambar'] = $filename;
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil diperbarui.');
    }

// Menghapus data alat
public function destroyAlat($id)
{
    $alat = Alat::findOrFail($id);

    // Cek apakah alat sedang dipinjam
    // Query: cari data di tabel detail_pinjam yang pake alat ID ini,
    // dan peminjamannya masih status dipinjam atau menunggu_verifikasi
    $sedangDipinjam = \App\Models\DetailPinjam::where('alat_id', $id)
        ->whereHas('peminjaman', function ($query) {
            $query->whereIn('status', ['dipinjam', 'menunggu_verifikasi']);
        })
        ->exists();

    // Kalo alat sedang dipinjam, tolak penghapusan
    if ($sedangDipinjam) {
        return redirect()->route('admin.alat.index')
            ->with('error', 'Alat "' . $alat->nama_alat . '" tidak dapat dihapus karena sedang dipinjam oleh user!');
    }

    // Hapus gambar jika ada
    if ($alat->gambar && file_exists(public_path('uploads/alats/' . $alat->gambar))) {
        unlink(public_path('uploads/alats/' . $alat->gambar));
    }

    // Hapus alat
    $alat->delete();

    return redirect()->back()->with('success', 'Alat berhasil dihapus.');
}

    // ==================== CRUD USER ====================

    // Menampilkan daftar user dengan search + pagination
    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('role', 'like', '%' . $search . '%');
        })
        ->latest()
        ->paginate(10)
        ->onEachSide(0)
        ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    // menampilkan form tambah user
    public function createUser()
    {
        return view('admin.user.create');
    }

    // menyimpan user baru
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:15',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    // menampilkan form edit user
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    // memperbarui data user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    // menghapus user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        // Cegah menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user.index')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    // ==================== CRUD KATEGORI ====================

    // 1. Menampilkan daftar kategori dengan search + pagination
    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', '%' . $search . '%');
        })
        ->latest()
        ->paginate(10)
        ->onEachSide(0)
        ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // 6. Menghapus kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Opsional: Cek apakah kategori masih dipakai oleh alat
        if ($kategori->alats()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // ==================== CRUD PEMINJAMAN ====================

    // 1. Menampilkan daftar peminjaman dengan search + pagination
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjaman', 'search'));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            // Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                // Validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,dikembalikan,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            // Logika pengelolaan stok otomatis
            if ($statusLama != 'dipinjam' && $statusBaru == 'dipinjam') {
                // Kurangi stok karena barang resmi dipinjam
                foreach ($peminjaman->detailPinjam as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama == 'dipinjam' && $statusBaru == 'dikembalikan') {
                // Kembalikan stok karena barang sudah dikembalikan
                foreach ($peminjaman->detailPinjam as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

        // Jika statusnya sedang dipinjam, kembalikan stok terlebih dahulu sebelum dihapus
        if ($peminjaman->status == 'dipinjam') {
            foreach ($peminjaman->detailPinjam as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // ==================== PENGEMBALIAN ====================

    // 1. Index - Daftar pengembalian dengan search + pagination
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->when($search, function ($query, $search) {
                return $query->where('kondisi_kembali', 'like', '%' . $search . '%')
                    ->orWhereHas('peminjaman.user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('pengembalians', 'search'));
    }

    // 2. Create - Form pengembalian dengan info denda
    public function createPengembalian()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->get();

        // Tambahkan info perhitungan denda ke setiap peminjaman
        foreach ($peminjamans as $peminjaman) {
            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $hariIni = Carbon::now()->startOfDay();

            if ($hariIni->greaterThan($tglKembaliPlan)) {
                $peminjaman->hariTelat = $tglKembaliPlan->diffInDays($hariIni);
                $peminjaman->totalDenda = $peminjaman->hariTelat * 5000;
                $peminjaman->statusDenda = 'telat';
            } else {
                $peminjaman->hariTelat = 0;
                $peminjaman->totalDenda = 0;
                $peminjaman->statusDenda = 'tepat';
            }
        }

        return view('admin.pengembalian.create', compact('peminjamans'));
    }

    // 3. Store - Proses pengembalian
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

            // Cek status peminjaman
            if ($peminjaman->status != 'dipinjam') {
                return back()->with('error', 'Peminjaman ini tidak sedang dipinjam.');
            }

            // ========== HITUNG DENDA ==========
            $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglKembaliAktual = Carbon::now()->startOfDay();

            // Denda telat otomatis (Rp 5.000/hari)
            $dendaTelat = 0;
            if ($tglKembaliAktual->greaterThan($tglKembaliPlan)) {
                $selisihHari = $tglKembaliPlan->diffInDays($tglKembaliAktual);
                $dendaTelat = $selisihHari * 5000;
            }

            // Denda tambahan (dari user) karena rusak/perbaikan
            $dendaTambahan = $request->denda_tambahan ?? 0;

            // Total denda
            $totalDenda = $dendaTelat + $dendaTambahan;

            // ========== SIMPAN PENGEMBALIAN ==========
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

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            // Log aktivitas
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => "Memproses pengembalian peminjaman ID: {$peminjaman->id} | Denda total: Rp " . number_format($totalDenda, 0, ',', '.'),
            ]);

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', "Pengembalian berhasil! Denda total: Rp " . number_format($totalDenda, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 4. Destroy - Hapus pengembalian
    public function destroyPengembalian($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($pengembalian->peminjaman_id);

            // Kembalikan status peminjaman ke 'dipinjam'
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok lagi (kembalikan ke kondisi sebelum pengembalian)
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => "Membatalkan pengembalian ID: {$pengembalian->id}",
            ]);

            $pengembalian->delete();

            DB::commit();

            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Data pengembalian berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}