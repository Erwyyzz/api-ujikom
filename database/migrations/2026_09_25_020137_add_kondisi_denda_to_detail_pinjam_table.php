<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pinjam', function (Blueprint $table) {
            // Kondisi alat saat dikembalikan (diisi petugas pas verifikasi)
            $table->enum('kondisi_kembali', ['baik', 'rusak', 'perbaikan'])
                  ->nullable()
                  ->after('jumlah');

            // Denda tambahan per-alat (kalau rusak/perbaikan)
            $table->integer('denda_tambahan')
                  ->default(0)
                  ->after('kondisi_kembali');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pinjam', function (Blueprint $table) {
            $table->dropColumn(['kondisi_kembali', 'denda_tambahan']);
        });
    }
};