<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_BERHASIL = 'berhasil';

    public const STATUS_GAGAL = 'gagal';

    public const STATUS_KEDALUWARSA = 'kedaluwarsa';

    public const STATUS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_BERHASIL => 'Berhasil',
        self::STATUS_GAGAL => 'Gagal',
        self::STATUS_KEDALUWARSA => 'Kedaluwarsa',
    ];

    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'order_id_gateway',
        'metode',
        'jumlah',
        'status',
        'dibayar_pada',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'dibayar_pada' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function sudahBerhasil(): bool
    {
        return $this->status === self::STATUS_BERHASIL;
    }
}
