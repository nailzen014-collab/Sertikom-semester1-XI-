<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diskon extends Model
{
    use HasFactory;

    public const TIPE_PERSEN = 'persen';

    public const TIPE_NOMINAL = 'nominal';

    public const TIPE = [
        self::TIPE_PERSEN => 'Persen (%)',
        self::TIPE_NOMINAL => 'Nominal (Rp)',
    ];

    protected $table = 'diskon';

    protected $fillable = [
        'admin_id',
        'nama',
        'kategori',
        'tipe',
        'nilai',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'status_aktif' => 'boolean',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', true);
    }

    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeBerlakuPada(Builder $query, $tanggal): Builder
    {
        $tanggal = $tanggal->toDateString();

        return $query->where('status_aktif', true)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal);
    }
}
