<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    /**
     * GET - Menampilkan daftar semua alat
     * 
     * Endpoint: GET /api/alat
     * Akses: Semua user (auth:sanctum)
     */
    public function index(): JsonResponse
    {
        // Eager loading untuk menghindari N+1 Query (memuat relasi kategori sekaligus)
        $alat = Alat::with('kategori')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar alat berhasil diambil.',
            'data' => AlatResource::collection($alat) // Transformasi data pakai Resource
        ]);
    }

    /**
     * POST - Menambahkan alat baru
     * 
     * Endpoint: POST /api/alat
     * Akses: Admin only (auth:sanctum + role:admin)
     * 
     * Validasi: StoreAlatRequest
     * - kategori_id: required|integer|exists:kategori,id
     * - nama_alat: required|string|max:255
     * - kode_alat: required|string|max:50|unique:alat,kode_alat
     * - stok: required|integer|min:0
     * - kondisi: required|in:baik,rusak,perbaikan
     * - deskripsi: nullable|string
     * - gambar: nullable|image|mimes:jpeg,png,jpg|max:2048
     */
    public function store(StoreAlatRequest $request): JsonResponse
    {
        // Ambil data yang sudah divalidasi dari FormRequest
        $data = $request->validated();

        // Gunakan transaksi agar jika ada error, data tidak tersimpan sebagian
        $alat = DB::transaction(function () use ($request, $data) {
            
            // Jika ada file gambar yang diupload
            if ($request->hasFile('gambar')) {
                // Simpan gambar di folder 'alat' pada disk 'public'
                // Hasilnya: 'alat/nama-file-gambar.jpg'
                $data['gambar'] = $request->file('gambar')->store('alat', 'public');
            }

            // Buat data alat baru di database
            return Alat::create($data);
        });

        // Catat Log Aktivitas (siapa yang nambah, kapan, dan apa)

        // Response sukses dengan data alat yang baru dibuat
        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil ditambahkan.',
            'data' => new AlatResource($alat->load('kategori')) // Load relasi kategori
        ], 201); // HTTP Status 201 = Created
    }

    /**
     * GET - Menampilkan detail satu alat berdasarkan ID
     * 
     * Endpoint: GET /api/alat/{id}
     * Akses: Semua user (auth:sanctum)
     * 
     * Route Model Binding: Laravel otomatis mencari Alat berdasarkan ID di URL
     * $alat adalah objek Alat yang sudah ditemukan
     */
    public function show(Alat $alat): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new AlatResource($alat->load('kategori')) // Load kategori agar lengkap
        ]);
    }

    /**
     * PUT/PATCH - Memperbarui data alat
     * 
     * Endpoint: PUT /api/alat/{id}
     * Akses: Admin only (auth:sanctum + role:admin)
     * 
     * Validasi: UpdateAlatRequest
     * - Semua field opsional, kecuali yang wajib
     * - kode_alat harus unique kecuali dirinya sendiri
     */
    public function update(UpdateAlatRequest $request, Alat $alat): JsonResponse
    {
        // Ambil data yang sudah divalidasi
        $data = $request->validated();

        // Simpan path gambar lama untuk dihapus nanti
        $oldGambar = $alat->gambar;

        DB::transaction(function () use ($request, $data, $alat, $oldGambar) {
            
            // Jika ada file gambar baru yang diupload
            if ($request->hasFile('gambar')) {
                // Simpan gambar baru
                $data['gambar'] = $request->file('gambar')->store('alat', 'public');

                // Hapus gambar lama dari server setelah gambar baru sukses disimpan
                if ($oldGambar) {
                    Storage::disk('public')->delete($oldGambar);
                }
            }

            // Update data alat di database
            $alat->update($data);
        });

        // Catat Log Aktivitas

        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil diperbarui.',
            'data' => new AlatResource($alat->load('kategori'))
        ]);
    }

    /**
     * DELETE - Menghapus alat berdasarkan ID
     * 
     * Endpoint: DELETE /api/alat/{id}
     * Akses: Admin only (auth:sanctum + role:admin)
     * 
     * NOTE: Sebelum hapus, cek apakah alat masih dipinjam?
     * Bisa ditambahkan validasi jika alat masih dalam status dipinjam
     */
    public function destroy(Alat $alat): JsonResponse
    {
        DB::transaction(function () use ($alat) {
            
            // Hapus file gambar dari server jika ada
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }

            // Simpan nama alat untuk log sebelum dihapus
            $namaAlat = $alat->nama_alat;
            $kodeAlat = $alat->kode_alat;

            // Hapus data alat dari database
            $alat->delete();

            // Catat Log Aktivitas
        });

        return response()->json([
            'success' => true,
            'message' => 'Alat berhasil dihapus.'
        ]);
    }

    /**
     * GET - Katalog alat yang tersedia (stok > 0 dan kondisi baik)
     * 
     * Endpoint: GET /api/alat/katalog
     * Akses: Semua user (auth:sanctum)
     * 
     * Digunakan oleh Peminjam untuk melihat alat yang bisa dipinjam
     */
    public function katalog(): JsonResponse
    {
        // Ambil alat dengan stok > 0, kondisi 'baik', dan urutkan terbaru
        $alat = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->where('kondisi', 'baik')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Katalog alat tersedia.',
            'data' => AlatResource::collection($alat)
        ]);
    }

    /**
     * GET - Cek stok alat berdasarkan ID
     * 
     * Endpoint: GET /api/alat/{id}/cek-stok
     * Akses: Semua user (auth:sanctum)
     * 
     * Berguna untuk mengecek ketersediaan alat sebelum peminjaman
     */
    public function cekStok($id): JsonResponse
    {
        $alat = Alat::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $alat->id,
                'nama_alat' => $alat->nama_alat,
                'stok' => $alat->stok,
                'kondisi' => $alat->kondisi,
                'tersedia' => $alat->stok > 0 && $alat->kondisi == 'baik'
            ]
        ]);
    }
}