<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TiketPaket extends Model
{
    use HasFactory;

    public const KATEGORI_UMUM = 'umum';

    public const KATEGORI_ROMBONGAN = 'rombongan';

    public const KATEGORI = [
        self::KATEGORI_UMUM => 'Umum',
        self::KATEGORI_ROMBONGAN => 'Rombongan',
    ];

    protected $table = 'tiket_paket';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'fasilitas',
        'harga',
        'kuota_harian',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'kuota_harian' => 'integer',
            'status_aktif' => 'boolean',
        ];
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', true);
    }

    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}
