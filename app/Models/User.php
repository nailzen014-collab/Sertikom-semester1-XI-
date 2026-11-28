<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama', 'email', 'password', 'no_hp'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PENGUNJUNG = 'pengunjung';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_PENGUNJUNG,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->isSuperadmin() || $this->role === self::ROLE_ADMIN;
    }

    public function labelRole(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => 'Superadmin',
            self::ROLE_ADMIN => 'Administrator',
            default => 'Pengunjung',
        };
    }

    public function diskonDibuat(): HasMany
    {
        return $this->hasMany(Diskon::class, 'admin_id');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }
}
