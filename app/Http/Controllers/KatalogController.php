<?php

namespace App\Http\Controllers;

use App\Models\TiketPaket;
use App\Services\HitungHargaService;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function show(TiketPaket $tiketPaket, HitungHargaService $harga): View
    {
        abort_unless($tiketPaket->status_aktif, 404);

        return view('katalog.show', [
            'tiket' => $tiketPaket,
            'diskon' => $harga->diskonTerbaik($tiketPaket, 1, today()),
        ]);
    }
}
