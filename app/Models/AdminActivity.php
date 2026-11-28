<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivity extends Model
{
    public const AKSI_DITAMBAHKAN = 'menambahkan';

    public const AKSI_DIPERBARUI = 'memperbarui';

    public const AKSI_DIHAPUS = 'menghapus';

    protected $fillable = [
        'actor_id',
        'actor_nama',
        'aksi',
        'subjek_id',
        'subjek_nama',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Catat aksi pengelolaan akun admin agar terlihat oleh admin lain.
     */
    public static function catat(User $actor, string $aksi, User $subjek): self
    {
        return self::create([
            'actor_id' => $actor->id,
            'actor_nama' => $actor->nama,
            'aksi' => $aksi,
            'subjek_id' => $subjek->id,
            'subjek_nama' => $subjek->nama,
        ]);
    }
}
