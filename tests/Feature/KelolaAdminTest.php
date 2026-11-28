<?php

use App\Models\AdminActivity;
use App\Models\User;

it('mengizinkan superadmin membuka halaman kelola admin', function () {
    $superadmin = User::factory()->superadmin()->create();

    $this->actingAs($superadmin)
        ->get(route('admin.kelola-admin.index'))
        ->assertOk()
        ->assertSee('Kelola Admin')
        ->assertSee('Superadmin');
});

it('menolak admin biasa membuka halaman kelola admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.kelola-admin.index'))
        ->assertForbidden();
});

it('menolak pengunjung membuka halaman kelola admin', function () {
    $pengunjung = User::factory()->create();

    $this->actingAs($pengunjung)
        ->get(route('admin.kelola-admin.index'))
        ->assertForbidden();
});

it('membuat akun admin baru dan mencatat aktivitas', function () {
    $superadmin = User::factory()->superadmin()->create();

    $this->actingAs($superadmin)
        ->post(route('admin.kelola-admin.store'), [
            'nama' => 'Admin Baru',
            'email' => 'adminbaru@example.com',
            'no_hp' => '081200000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('admin.kelola-admin.index'));

    $admin = User::where('email', 'adminbaru@example.com')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe(User::ROLE_ADMIN)
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->isSuperadmin())->toBeFalse();

    $activity = AdminActivity::latest('id')->first();

    expect($activity->actor_id)->toBe($superadmin->id)
        ->and($activity->aksi)->toBe(AdminActivity::AKSI_DITAMBAHKAN)
        ->and($activity->subjek_nama)->toBe('Admin Baru');
});

it('memperbarui akun admin tanpa mengubah kata sandi bila kosong', function () {
    $superadmin = User::factory()->superadmin()->create();
    $admin = User::factory()->admin()->create(['email' => 'lama@example.com']);
    $passwordLama = $admin->password;

    $this->actingAs($superadmin)
        ->put(route('admin.kelola-admin.update', $admin), [
            'nama' => 'Admin Diubah',
            'email' => 'baru@example.com',
            'no_hp' => '081211111111',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('admin.kelola-admin.index'));

    $admin->refresh();

    expect($admin->nama)->toBe('Admin Diubah')
        ->and($admin->email)->toBe('baru@example.com')
        ->and($admin->password)->toBe($passwordLama)
        ->and(AdminActivity::latest('id')->first()->aksi)->toBe(AdminActivity::AKSI_DIPERBARUI);
});

it('menghapus akun admin dan mencatat aktivitas', function () {
    $superadmin = User::factory()->superadmin()->create();
    $admin = User::factory()->admin()->create();
    $namaAdmin = $admin->nama;

    $this->actingAs($superadmin)
        ->delete(route('admin.kelola-admin.destroy', $admin))
        ->assertRedirect(route('admin.kelola-admin.index'));

    expect(User::find($admin->id))->toBeNull();

    $activity = AdminActivity::latest('id')->first();

    expect($activity->aksi)->toBe(AdminActivity::AKSI_DIHAPUS)
        ->and($activity->subjek_nama)->toBe($namaAdmin);
});

it('tidak mengizinkan superadmin mengubah atau menghapus akun superadmin lain', function () {
    $superadmin = User::factory()->superadmin()->create();
    $lain = User::factory()->superadmin()->create();

    $this->actingAs($superadmin)
        ->get(route('admin.kelola-admin.edit', $lain))
        ->assertNotFound();

    $this->actingAs($superadmin)
        ->delete(route('admin.kelola-admin.destroy', $lain))
        ->assertNotFound();
});

it('menampilkan formulir tambah dan ubah admin', function () {
    $superadmin = User::factory()->superadmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($superadmin)
        ->get(route('admin.kelola-admin.create'))
        ->assertOk()
        ->assertSee('Tambah Admin');

    $this->actingAs($superadmin)
        ->get(route('admin.kelola-admin.edit', $admin))
        ->assertOk()
        ->assertSee('Ubah Admin');
});

it('menjadikan akun admin bawaan seeder sebagai superadmin', function () {
    $this->seed();

    expect(User::where('email', 'admin@thenicepark.test')->first()->role)
        ->toBe(User::ROLE_SUPERADMIN);
});

it('menampilkan badge aktivitas kepada admin biasa di dashboard', function () {
    $superadmin = User::factory()->superadmin()->create(['nama' => 'Super Satu']);
    $admin = User::factory()->admin()->create();
    $baru = User::factory()->admin()->create(['nama' => 'Admin Dua']);

    AdminActivity::catat($superadmin, AdminActivity::AKSI_DITAMBAHKAN, $baru);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Aktivitas pengelola')
        ->assertSee('Super Satu menambahkan Admin Dua');
});
