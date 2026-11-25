<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_transaksi';

    /**
     * @var string
     */
    protected $primaryKey = 'id_parkir';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_kendaraan',
        'waktu_masuk',
        'waktu_keluar',
        'id_tarif',
        'durasi_jam',
        'biaya_total',
        'status',
        'id_user',
        'id_area',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_masuk' => 'datetime',
            'waktu_keluar' => 'datetime',
            'durasi_jam' => 'integer',
            'biaya_total' => 'decimal:0',
            'id_kendaraan' => 'integer',
            'id_tarif' => 'integer',
            'id_user' => 'integer',
            'id_area' => 'integer',
        ];
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function tarif(): BelongsTo
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('status', 'masuk');
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('status', 'keluar');
    }

    public function isMasuk(): bool
    {
        return $this->status === 'masuk';
    }
}
