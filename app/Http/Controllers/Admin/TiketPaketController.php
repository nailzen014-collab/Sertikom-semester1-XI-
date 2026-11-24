<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TiketPaketRequest;
use App\Models\TiketPaket;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TiketPaketController extends Controller
{
    public function index(): View
    {
        return view('admin.tiket-paket.index', [
            'tiketPaket' => TiketPaket::latest()->paginate(5),
        ]);
    }

    public function create(): View
    {
        return view('admin.tiket-paket.create', [
            'tiket' => new TiketPaket,
        ]);
    }

    public function store(TiketPaketRequest $request): RedirectResponse
    {
        TiketPaket::create($this->attributes($request));

        return redirect()->route('admin.tiket-paket.index')
            ->with('message', 'Tiket/paket berhasil ditambahkan.');
    }

    public function edit(TiketPaket $tiketPaket): View
    {
        return view('admin.tiket-paket.edit', [
            'tiket' => $tiketPaket,
        ]);
    }

    public function update(TiketPaketRequest $request, TiketPaket $tiketPaket): RedirectResponse
    {
        $tiketPaket->update($this->attributes($request));

        return redirect()->route('admin.tiket-paket.index')
            ->with('message', 'Tiket/paket berhasil diperbarui.');
    }

    public function destroy(TiketPaket $tiketPaket): RedirectResponse
    {
        $tiketPaket->delete();

        return redirect()->route('admin.tiket-paket.index')
            ->with('message', 'Tiket/paket berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(TiketPaketRequest $request): array
    {
        return [
            ...$request->validated(),
            'status_aktif' => $request->boolean('status_aktif'),
        ];
    }
}
