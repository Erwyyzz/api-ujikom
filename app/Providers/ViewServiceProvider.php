<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Badge untuk layout petugas
        View::composer('layouts.petugas', function ($view) {
            $user = Auth::user();
            if (!$user) return;

            // Hitung pengajuan baru sejak terakhir buka menu
            $badgePeminjaman = Peminjaman::where('status', 'diajukan')
                ->when($user->peminjaman_last_read_at, function ($q) use ($user) {
                    $q->where('created_at', '>', $user->peminjaman_last_read_at);
                })
                ->count();

            // Hitung pengembalian baru sejak terakhir buka menu
            $badgePengembalian = Peminjaman::where('status', 'menunggu_verifikasi')
                ->when($user->pengembalian_last_read_at, function ($q) use ($user) {
                    $q->where('updated_at', '>', $user->pengembalian_last_read_at);
                })
                ->count();

            $view->with('badgePeminjaman', $badgePeminjaman);
            $view->with('badgePengembalian', $badgePengembalian);
        });

        // Badge untuk layout peminjam
        View::composer('layouts.peminjam', function ($view) {
            $user = Auth::user();
            if (!$user) return;

            // Hitung alat dipinjam yang dibuat SETELAH terakhir buka menu Pengembalian Alat
            $badgeDipinjam = Peminjaman::where('user_id', $user->id)
                ->where('status', 'dipinjam')
                ->when($user->pengembalian_alat_last_read_at, function ($q) use ($user) {
                    $q->where('updated_at', '>', $user->pengembalian_alat_last_read_at);
                })
                ->count();

            // Hitung yang menunggu verifikasi SETELAH terakhir buka menu Riwayat
            $badgeMenungguVerifikasi = Peminjaman::where('user_id', $user->id)
                ->where('status', 'menunggu_verifikasi')
                ->when($user->riwayat_last_read_at, function ($q) use ($user) {
                    $q->where('updated_at', '>', $user->riwayat_last_read_at);
                })
                ->count();

            $view->with('badgeDipinjam', $badgeDipinjam);
            $view->with('badgeMenungguVerifikasi', $badgeMenungguVerifikasi);
        });
        }
}