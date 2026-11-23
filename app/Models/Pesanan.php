<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    public const BATAS_PESANAN_UMUM_PER_AKUN = 5;

    public const STATUS_MENUNGGU_PERSETUJUAN = 'menunggu_persetujuan';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const STATUS_LUNAS = 'lunas';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_KEDALUWARSA = 'kedaluwarsa';

    public const STATUS = [
        self::STATUS_MENUNGGU_PERSETUJUAN => 'Menunggu Persetujuan',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
        self::STATUS_LUNAS => 'Lunas',
        self::STATUS_DITOLAK => 'Ditolak',
        self::STATUS_KEDALUWARSA => 'Kedaluwarsa',
    ];

    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'tiket_paket_id',
        'diskon_id',
        'kategori',
        'tanggal_kunjungan',
        'jumlah_tiket',
        'harga_satuan',
        'potongan_diskon',
        'total_bayar',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kunjungan' => 'date',
            'jumlah_tiket' => 'integer',
            'harga_satuan' => 'integer',
            'potongan_diskon' => 'integer',
            'total_bayar' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tiketPaket(): BelongsTo
    {
        return $this->belongsTo(TiketPaket::class);
    }

    public function diskon(): BelongsTo
    {
        return $this->belongsTo(Diskon::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function tiket(): HasMany
    {
        return $this->hasMany(Tiket::class);
    }

    public function pengajuanRombongan(): HasOne
    {
        return $this->hasOne(PengajuanRombongan::class);
    }

    public function pesertaRombongan(): HasMany
    {
        return $this->hasMany(PesertaRombongan::class);
    }

    public function scopeLunas(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_LUNAS);
    }

    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }
}
