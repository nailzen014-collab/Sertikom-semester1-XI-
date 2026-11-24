<?php

namespace App\Policies;

use App\Models\Pesanan;
use App\Models\User;

class PesananPolicy
{
    public function view(User $user, Pesanan $pesanan): bool
    {
        return $pesanan->user_id === $user->id;
    }
}
