<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_tarif';

    /**
     * @var string
     */
    protected $primaryKey = 'id_tarif';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'jenis_kendaraan',
        'tarif_per_jam',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tarif_per_jam' => 'decimal:0',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_tarif', 'id_tarif');
    }
}
