<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiskonRequest;
use App\Models\Diskon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiskonController extends Controller
{
    public function index(): View
    {
        return view('admin.diskon.index', [
            'diskon' => Diskon::with('admin')->latest()->paginate(5),
        ]);
    }

    public function create(): View
    {
        return view('admin.diskon.create', [
            'diskon' => new Diskon,
        ]);
    }

    public function store(DiskonRequest $request): RedirectResponse
    {
        Diskon::create([
            ...$request->validated(),
            'admin_id' => $request->user()->id,
            'status_aktif' => $request->boolean('status_aktif'),
        ]);

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
}
