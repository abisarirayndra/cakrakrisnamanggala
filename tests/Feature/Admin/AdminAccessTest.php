<?php

namespace Tests\Feature\Admin;

use App\AbsensiPelajar;
use App\Jadwal;
use App\Kelas;
use App\Mapel;
use App\Markas;
use App\User;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use CreatesAdminMasterSchema;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpAdminMasterSchema();
    }

    public function test_non_super_admin_can_open_cat_paket(): void
    {
        $response = $this->actingAs($this->markasAdmin())
            ->get(route('admin.dinas.paket'));

        $this->assertNotSame(403, $response->status());
    }

    public function test_super_admin_can_open_cat_paket_route(): void
    {
        $response = $this->actingAs($this->superAdmin())
            ->get(route('admin.dinas.paket'));

        $this->assertNotSame(403, $response->status());
    }

    public function test_non_admin_is_forbidden_on_admin_beranda(): void
    {
        $pelajar = User::factory()->create(['role_id' => 4]);

        $this->actingAs($pelajar)
            ->get(route('admin.beranda'))
            ->assertForbidden();
    }

    public function test_staf_admin_operational_routes_are_restored(): void
    {
        $this->assertTrue(Route::has('staf-admin.jadwal'));
        $this->assertTrue(Route::has('staf-admin.jadwal.tambah'));
        $this->assertTrue(Route::has('staf-admin.jadwal.hapus'));
        $this->assertTrue(Route::has('staf-admin.jadwal.edit'));
        $this->assertTrue(Route::has('staf-admin.jadwal.update'));
        $this->assertTrue(Route::has('staf-admin.absen-pulang'));
        $this->assertTrue(Route::has('staf-admin.absensi.beranda'));
        $this->assertTrue(Route::has('staf-admin.absensi.upload-absensi'));
    }

    public function test_legacy_jadwal_index_redirects_to_admin_jadwal(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('staf-admin.jadwal'))
            ->assertRedirect(route('admin.jadwal'));
    }

    public function test_legacy_jadwal_hapus_redirects_and_does_not_delete(): void
    {
        $admin = $this->markasAdmin();
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $admin->markasIds()[0]]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        $this->actingAs($admin)
            ->get(route('staf-admin.jadwal.hapus', $row->id))
            ->assertRedirect(route('admin.jadwal'));

        $this->assertDatabaseHas('adm_jadwal', ['id' => $row->id]);
    }

    public function test_named_routes_are_unique(): void
    {
        $names = collect(app('router')->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $duplicates = $names->duplicates()->unique()->values()->all();

        $this->assertSame([], $duplicates, 'Duplicate route names: '.implode(', ', $duplicates));
    }

    public function test_beranda_hides_admin_from_non_super_and_shows_sidebar_nav(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSeeInOrder([
                'data-nav="beranda"',
                'data-nav="pendaftar"',
                'data-nav="pelajar"',
                'data-nav="pendidik"',
                'data-nav="jadwal"',
                'data-nav="absensi"',
                'data-nav="cat"',
            ], false)
            ->assertDontSee('data-nav="admin"', false)
            ->assertDontSee('data-nav="markas"', false)
            ->assertDontSee('data-nav="kelas"', false)
            ->assertDontSee('data-nav="mapel"', false)
            ->assertSee(route('admin.pengguna.pendaftar', absolute: false), false)
            ->assertSee(route('admin.jadwal', absolute: false), false)
            ->assertSee(route('admin.absensi', absolute: false), false)
            ->assertSee(route('admin.dinas.paket', absolute: false), false);
    }

    public function test_legacy_absensi_beranda_redirects_to_admin_absensi(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('staf-admin.absensi.beranda'))
            ->assertRedirect(route('admin.absensi'));
    }

    public function test_legacy_absensi_upload_redirects_and_does_not_write(): void
    {
        $this->actingAs($this->markasAdmin())
            ->post(route('staf-admin.absensi.upload-absensi'), ['token' => 'ABC123'])
            ->assertRedirect(route('admin.absensi'));

        $this->assertSame(0, AbsensiPelajar::count());
    }

    public function test_beranda_shows_admin_and_cat_for_super(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Superadmin')
            ->assertSee('ck-sidebar', false)
            ->assertSeeInOrder([
                'data-nav="beranda"',
                'data-nav="pendaftar"',
                'data-nav="pelajar"',
                'data-nav="pendidik"',
                'data-nav="admin"',
                'data-nav="markas"',
                'data-nav="kelas"',
                'data-nav="mapel"',
                'data-nav="jadwal"',
                'data-nav="absensi"',
                'data-nav="cat"',
            ], false);
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }

    private function markasAdmin(): User
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $user = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
        ]);
        $user->markas()->attach($markas->id);

        return $user;
    }
}
