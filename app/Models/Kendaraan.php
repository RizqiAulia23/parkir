<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kendaraan extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_kendaraan';

    /**
     * @var string
     */
    protected $primaryKey = 'id_kendaraan';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'plat_nomor',
        'jenis_kendaraan',
        'warna',
        'pemilik',
        'id_user',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_user' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function transaksiAktif(): HasOne
    {
        return $this->hasOne(Transaksi::class, 'id_kendaraan', 'id_kendaraan')
            ->where('status', 'masuk');
    }

    public function isSedangParkir(): bool
    {
        return $this->transaksi()->where('status', 'masuk')->exists();
    }
}
