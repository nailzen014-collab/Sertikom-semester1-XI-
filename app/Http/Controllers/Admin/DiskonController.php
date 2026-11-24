<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiskonRequest;
use App\Models\Diskon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiskonController extends Controller
{
    public function index(): View
    {
        return view('admin.diskon.index', [
            'diskon' => Diskon::with(['admin', 'tiketPaket'])->latest()->paginate(5),
        ]);
    }

    public function create(): View
    {
        return view('admin.diskon.create', [
            'diskon' => new Diskon,
            'diskonAktifPerPaket' => $this->diskonAktifPerPaket(),
        ]);
    }

    public function store(DiskonRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $gantiDiskon = $request->boolean('ganti_diskon');
        unset($validated['ganti_diskon']);

        DB::transaction(function () use ($validated, $gantiDiskon, $request) {
            if ($gantiDiskon) {
                Diskon::berlakuPada(now())
                    ->untukPaket((int) $validated['tiket_paket_id'])
                    ->update(['status_aktif' => false]);
            }

            Diskon::create([
                ...$validated,
                'admin_id' => $request->user()->id,
                'status_aktif' => $request->boolean('status_aktif'),
            ]);
        });

        return redirect()->route('admin.diskon.index')
            ->with('message', 'Diskon berhasil ditambahkan.');
    }

    public function edit(Diskon $diskon): View
    {
        return view('admin.diskon.edit', [
            'diskon' => $diskon,
        ]);
    }

    public function update(DiskonRequest $request, Diskon $diskon): RedirectResponse
    {
        $diskon->update([
            ...$request->validated(),
            'status_aktif' => $request->boolean('status_aktif'),
        ]);

        return redirect()->route('admin.diskon.index')
            ->with('message', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Diskon $diskon): RedirectResponse
    {
        $diskon->delete();

        return redirect()->route('admin.diskon.index')
            ->with('message', 'Diskon berhasil dihapus.');
    }

    /**
     * Diskon yang sedang aktif per paket, untuk mendeteksi potensi bentrok
     * saat admin menambah diskon baru pada paket yang sama.
     *
     * @return array<int, array<int, array{id: int, nama: string, tipe: string, nilai: int}>>
     */
    private function diskonAktifPerPaket(): array
    {
        return Diskon::berlakuPada(now())
            ->get()
            ->map(fn (Diskon $diskon) => [
                'tiket_paket_id' => $diskon->tiket_paket_id,
                'id' => $diskon->id,
                'nama' => $diskon->nama,
                'tipe' => $diskon->tipe,
                'nilai' => $diskon->nilai,
            ])
            ->groupBy('tiket_paket_id')
            ->map(fn ($items) => collect($items)->map(fn ($item) => [
                'id' => $item['id'],
                'nama' => $item['nama'],
                'tipe' => $item['tipe'],
                'nilai' => $item['nilai'],
            ])->values()->all())
            ->all();
    }
}
