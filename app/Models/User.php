<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'foto_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',     //laravel otomatis meng hash teks apapun yang masuk ke proprti password
        ];
    }

    // Relasi ke peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    // Relasi ke pengembalian (sebagai petugas)
    public function pengembalian()
    {
        return $this->hasMany(Pengembalian::class, 'petugas_id');
    }

    // Relasi ke log aktivitas
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class);
    }

    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0)->where('status_kondisi', 'Baik');
    }
}
