<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('user.orders');
    }

    public function orders(Request $request): View
    {
        return view('user.dashboard', [
            'page' => 'orders',
            'pageTitle' => 'Pesanan Saya - The Nice Park',
            'pesanan' => $request->user()->pesanan()
                ->with([
                    'tiketPaket',
                    'pembayaran',
                    'tiket',
                    'pengajuanRombongan',
                    'pesertaRombongan',
                ])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Pesanan $pesanan): View
    {
        $this->authorize('view', $pesanan);

        $pesanan->load([
            'tiketPaket',
            'pembayaran',
            'tiket',
            'pengajuanRombongan',
            'pesertaRombongan',
        ]);

        return view('user.orders.show', [
            'pesanan' => $pesanan,
            'pageTitle' => 'Detail Pesanan - The Nice Park',
        ]);
    }
}
