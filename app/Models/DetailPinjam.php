<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPinjam extends Model
{
    protected $table = 'detail_pinjam';
    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
        'kondisi_kembali',
        'denda_tambahan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'denda_tambahan' => 'integer',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }
}