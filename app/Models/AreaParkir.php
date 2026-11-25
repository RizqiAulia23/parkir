<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaParkir extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_area_parkir';

    /**
     * @var string
     */
    protected $primaryKey = 'id_area';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'terisi' => 'integer',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_area', 'id_area');
    }

    public function sisaKapasitas(): int
    {
        return max(0, $this->kapasitas - $this->terisi);
    }

    public function isPenuh(): bool
    {
        return $this->terisi >= $this->kapasitas;
    }
}
