<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LogAktivitas extends Model
{
    /**
     * @var string
     */
    protected $table = 'tb_log_aktivitas';

    /**
     * @var string
     */
    protected $primaryKey = 'id_log';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'aktivitas',
        'waktu_aktivitas',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_aktivitas' => 'datetime',
            'id_user' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Catat aktivitas penting ke tb_log_aktivitas.
     */
    public static function catat(string $aktivitas, ?int $idUser = null): self
    {
        return static::create([
            'id_user' => $idUser ?? auth()->id(),
            'aktivitas' => Str::limit($aktivitas, 100, ''),
            'waktu_aktivitas' => now(),
        ]);
    }
}
