<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePesananRequest;
use App\Models\Pesanan;
use App\Models\TiketPaket;
use App\Models\User;
use App\Services\HitungHargaService;
use App\Services\KuotaService;
use App\Services\TiketGenerator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    public function kategori(Request $request): RedirectResponse
    {
        $url = route('user.booking');

        return redirect()->to($request->query('kategori') === TiketPaket::KATEGORI_ROMBONGAN
            ? $url.'#paket-rombongan'
            : $url);
    }

    public function create(TiketPaket $tiketPaket): RedirectResponse
    {
        if ($redirect = $this->pastikanUmum($tiketPaket)) {
            return $redirect;
        }

        return redirect()->to(route('user.booking').'#paket-'.$tiketPaket->id);
    }

    public function store(
        StorePesananRequest $request,
        TiketPaket $tiketPaket,
        HitungHargaService $harga,
        KuotaService $kuota,
        TiketGenerator $generator,
    ): RedirectResponse {
        if ($redirect = $this->pastikanUmum($tiketPaket)) {
            return $redirect;
        }

        $data = $request->validated();
        $jumlah = (int) $data['jumlah_tiket'];

        if ($jumlah > 10) {
            return to_route('pesan.rombongan.create')
                ->withInput([
                    'tanggal_kunjungan' => $data['tanggal_kunjungan'],
                    'jumlah_tiket' => $jumlah,
                ])
                ->with('message', 'Pesanan lebih dari 10 tiket harus diajukan sebagai rombongan.');
        }

        $pesanan = DB::transaction(function () use ($request, $tiketPaket, $data, $jumlah, $harga, $kuota, $generator) {
            User::query()->whereKey($request->user()->id)->lockForUpdate()->firstOrFail();

            if ($request->user()->pesanan()->kategori(TiketPaket::KATEGORI_UMUM)->count() >= Pesanan::BATAS_PESANAN_UMUM_PER_AKUN) {
                throw ValidationException::withMessages([
                    'jumlah_tiket' => 'Setiap akun maksimal dapat membuat 5 pesanan tiket umum.',
                ]);
            }

            $paket = TiketPaket::whereKey($tiketPaket->id)->lockForUpdate()->first();

            if (! $paket->status_aktif) {
                throw ValidationException::withMessages([
                    'tiket' => 'Tiket/paket ini sedang tidak aktif.',
                ]);
            }

            if ($kuota->tanggalTutup($data['tanggal_kunjungan'])) {
                throw ValidationException::withMessages([
                    'tanggal_kunjungan' => 'Tanggal yang dipilih sedang tutup. Silakan pilih tanggal lain.',
                ]);
            }

            if (! $kuota->cukup($paket, $data['tanggal_kunjungan'], $jumlah, $request->user()->id)) {
                throw ValidationException::withMessages([
                    'jumlah_tiket' => 'Kuota harian tidak mencukupi. Sisa kuota: '.$kuota->sisa($paket, $data['tanggal_kunjungan'], $request->user()->id).' tiket.',
                ]);
            }

            $diskon = $harga->diskonTerbaik($paket, $jumlah, Carbon::parse($data['tanggal_kunjungan']));

            return Pesanan::create([
                'kode_pesanan' => $generator->kodePesanan(),
                'user_id' => $request->user()->id,
                'tiket_paket_id' => $paket->id,
                'diskon_id' => $diskon?->id,
                'kategori' => $paket->kategori,
                'tanggal_kunjungan' => $data['tanggal_kunjungan'],
                'jumlah_tiket' => $jumlah,
                ...$harga->hitung($paket, $jumlah, $diskon),
                'status' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
            ]);
        });

        return to_route('user.orders', ['pesanan' => $pesanan->id]);
    }

    private function pastikanUmum(TiketPaket $tiketPaket): ?RedirectResponse
    {
        if ($tiketPaket->kategori !== TiketPaket::KATEGORI_UMUM) {
            return to_route('pesan.rombongan.create')
                ->with('message', 'Paket ini hanya dapat dipesan melalui pengajuan rombongan.');
        }

        return null;
    }
}
