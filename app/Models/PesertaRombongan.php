<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PesertaRombongan extends Model
{
    use HasFactory;

    protected $table = 'peserta_rombongan';

    protected $fillable = [
        'pesanan_id',
        'nama',
        'kelas',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function tiket(): HasOne
    {
        return $this->hasOne(Tiket::class, 'peserta_id');
    }
}
