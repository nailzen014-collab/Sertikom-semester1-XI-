<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\TiketPaket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function index(Request $request): View
    {
        $pesanan = Pesanan::with(['user', 'tiketPaket'])
            ->when($request->filled('tanggal'), fn ($query) => $query->whereDate('tanggal_kunjungan', $request->input('tanggal')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori', $request->input('kategori')))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.pesanan.index', [
            'pesanan' => $pesanan,
            'status' => Pesanan::STATUS,
            'kategori' => TiketPaket::KATEGORI,
        ]);
    }

    public function show(Pesanan $pesanan): View
    {
        $pesanan->load(['user', 'tiketPaket', 'diskon', 'pembayaran', 'tiket']);

        return view('admin.pesanan.show', [
            'pesanan' => $pesanan,
        ]);
    }
}
