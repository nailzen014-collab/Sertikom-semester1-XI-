<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;

class RiwayatController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->to(route('user.dashboard').'#pesanan');
    }

    public function show(Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === auth()->id(), 404);

        return to_route('user.dashboard', ['pesanan' => $pesanan->id]);
    }
}
