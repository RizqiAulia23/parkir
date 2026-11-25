<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'tb_user';

    /**
     * @var string
     */
    protected $primaryKey = 'id_user';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama_lengkap',
        'username',
        'password',
        'role',
        'status_aktif',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_aktif' => 'integer',
        ];
    }

    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'id_user', 'id_user');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_user', 'id_user');
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class, 'id_user', 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAktif(): bool
    {
        return (int) $this->status_aktif === 1;
    }
}
