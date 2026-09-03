<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AlatController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LogAktivitasController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\LaporanController;

// PUBLIC ROUTES (Tidak Perlu Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// PROTECTED ROUTES (Wajib Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // ========== AUTH ==========
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ========== ALAT (Semua Role Bisa Lihat) ==========
    Route::get('/alat', [AlatController::class, 'index']);
    Route::get('/alat/katalog', [AlatController::class, 'katalog']);
    Route::get('/alat/{id}', [AlatController::class, 'show']);
    Route::get('/alat/{id}/cek-stok', [AlatController::class, 'cekStok']);

    // ========== ADMIN ONLY ==========
    Route::middleware('role.admin')->group(function () {
        // ALAT (CRUD - Hanya Admin)
        Route::post('/alat', [AlatController::class, 'store']);
        Route::put('/alat/{id}', [AlatController::class, 'update']);
        Route::delete('/alat/{id}', [AlatController::class, 'destroy']);

        // KATEGORI (CRUD)
        Route::apiResource('kategori', KategoriController::class);

        // USER (CRUD)
        Route::apiResource('users', UserController::class);

        // ========== LOG AKTIVITAS (Hanya Admin) ==========
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
    });

    // ========== PEMINJAMAN ==========
    
    // Semua Role Bisa Akses (Read Only)
    Route::get('/peminjaman', [PeminjamanController::class, 'index']);
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);

    // Hanya Peminjam
    Route::middleware('role.peminjam')->group(function () {
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
        Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat']);
    });

    // Hanya Petugas
    Route::middleware('role.petugas')->group(function () {
        Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve']);
        Route::put('/peminjaman/{peminjaman}', [PeminjamanController::class, 'update']);
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);
    });

    // ========== DASHBOARD PER ROLE ==========
    Route::middleware('role.admin')->group(function () {
        Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard']);
    });

    Route::middleware('role.petugas')->group(function () {
        Route::get('/petugas/dashboard', [AuthController::class, 'petugasDashboard']);
    });

    Route::middleware('role.peminjam')->group(function () {
        Route::get('/peminjam/dashboard', [AuthController::class, 'peminjamDashboard']);
    });

    // ========== PENGEMBALIAN ==========

    // Semua Role Bisa Akses (Read Only)
    Route::get('/pengembalian', [PengembalianController::class, 'index']);
    Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);

    // Hanya Admin
    Route::middleware('role.admin')->group(function () {
        Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
        Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);
    });

    // Hanya Petugas
    Route::middleware('role.petugas')->group(function () {
        Route::post('/pengembalian', [PengembalianController::class, 'store']);
    });

    // ========== LAPORAN ==========
    Route::middleware('role.admin')->group(function () {
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']);
    });
});