<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanRombongan extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN = 'diajukan';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const MODE_PER_PESERTA = 'per_peserta';

    public const MODE_PER_ROMBONGAN = 'per_rombongan';

    protected $table = 'pengajuan_rombongan';

    protected $fillable = [
        'pesanan_id',
        'admin_id',
        'nama_sekolah',
        'penanggung_jawab',
        'no_hp',
        'mode_tiket',
        'status',
        'catatan_admin',
        'ditinjau_pada',
    ];

    protected function casts(): array
    {
        return [
            'ditinjau_pada' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
