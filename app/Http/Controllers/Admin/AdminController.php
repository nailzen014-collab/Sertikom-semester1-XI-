<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.kelola-admin.index', [
            'admin' => User::query()
                ->where('role', User::ROLE_ADMIN)
                ->latest('id')
                ->paginate(10),
            'aktivitas' => AdminActivity::latest('id')->limit(10)->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.kelola-admin.create', [
            'admin' => new User,
        ]);
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();

            $admin = new User([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'] ?? null,
                'password' => $data['password'],
            ]);
            $admin->role = User::ROLE_ADMIN;
            $admin->email_verified_at = now();
            $admin->save();

            AdminActivity::catat($request->user(), AdminActivity::AKSI_DITAMBAHKAN, $admin);
        });

        return redirect()->route('admin.kelola-admin.index')
            ->with('message', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(User $admin): View
    {
        $this->ensureManageable($admin);

        return view('admin.kelola-admin.edit', [
            'admin' => $admin,
        ]);
    }

    public function update(AdminRequest $request, User $admin): RedirectResponse
    {
        $this->ensureManageable($admin);

        DB::transaction(function () use ($request, $admin) {
            $data = $request->validated();

            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }

            $admin->fill($data);
            $admin->save();

            AdminActivity::catat($request->user(), AdminActivity::AKSI_DIPERBARUI, $admin);
        });

        return redirect()->route('admin.kelola-admin.index')
            ->with('message', 'Akun admin berhasil diperbarui.');
    }

    public function destroy(Request $request, User $admin): RedirectResponse
    {
        $this->ensureManageable($admin);

        DB::transaction(function () use ($request, $admin) {
            AdminActivity::catat($request->user(), AdminActivity::AKSI_DIHAPUS, $admin);
            $admin->delete();
        });

        return redirect()->route('admin.kelola-admin.index')
            ->with('message', 'Akun admin berhasil dihapus.');
    }

    /**
     * Hanya akun admin biasa yang boleh dikelola; superadmin tidak dapat diubah.
     */
    private function ensureManageable(User $admin): void
    {
        abort_unless($admin->role === User::ROLE_ADMIN, 404);
    }
}
