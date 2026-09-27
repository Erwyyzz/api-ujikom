<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Waktu terakhir peminjam buka menu Pengembalian Alat
            $table->timestamp('pengembalian_alat_last_read_at')->nullable();
            // Waktu terakhir peminjam buka menu Riwayat
            $table->timestamp('riwayat_last_read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pengembalian_alat_last_read_at', 'riwayat_last_read_at']);
        });
    }
};