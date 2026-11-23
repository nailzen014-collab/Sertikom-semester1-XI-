<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tiket extends Model
{
    use HasFactory;

    public const JENIS_INDIVIDU = 'individu';

    public const JENIS_ROMBONGAN = 'rombongan';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_DIPAKAI = 'dipakai';

    protected $table = 'tiket';

    protected $fillable = [
        'pesanan_id',
        'peserta_id',
        'divalidasi_oleh',
        'kode_tiket',
        'jenis',
        'status',
        'dipakai_pada',
    ];

    protected function casts(): array
    {
        return [
            'dipakai_pada' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(PesertaRombongan::class, 'peserta_id');
    }

    public function divalidasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}
