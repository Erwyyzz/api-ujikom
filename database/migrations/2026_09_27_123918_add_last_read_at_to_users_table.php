<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Waktu terakhir user buka menu Peminjaman
            $table->timestamp('peminjaman_last_read_at')->nullable();
            // Waktu terakhir user buka menu Pengembalian
            $table->timestamp('pengembalian_last_read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['peminjaman_last_read_at', 'pengembalian_last_read_at']);
        });
    }
};