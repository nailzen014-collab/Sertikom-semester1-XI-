<?php

namespace App\Http\Controllers;

use App\Models\Diskon;
use App\Models\TiketPaket;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $tiketPaket = TiketPaket::aktif()
            ->orderBy('harga')
            ->get()
            ->groupBy('kategori');

        $diskon = Diskon::berlakuPada(now())
            ->get()
            ->groupBy('tiket_paket_id');

        return view('landing', [
            'tiketPaket' => $tiketPaket,
            'diskon' => $diskon,
            'kategori' => TiketPaket::KATEGORI,
            'destinasi' => config('destinasi'),
        ]);
    }
}
