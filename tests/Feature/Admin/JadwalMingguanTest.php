<?php

namespace Tests\Feature\Admin;

use App\Jadwal;
use App\Kelas;
use App\Livewire\Admin\JadwalMingguan;
use App\Mapel;
use App\Markas;
use App\Support\AdminVisibility;
use App\User;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class JadwalMingguanTest extends TestCase
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

    public function test_admin_kelas_for_jadwal_is_limited_to_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $a = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        Kelas::create(['nama' => 'Tanpa Markas', 'markas_id' => null]);

        $ids = AdminVisibility::kelasForJadwal($admin)->pluck('kelas.id')->all();
        $this->assertEquals([$a->id], $ids);
    }

    public function test_admin_jadwal_query_hides_other_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $ka = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $kb = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        $mine = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $ka->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kb->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        $ids = AdminVisibility::jadwalQuery($admin)->pluck('adm_jadwal.id')->all();
        $this->assertEquals([$mine->id], $ids);
    }

    public function test_non_admin_cannot_mount_jadwal_mingguan(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(JadwalMingguan::class)
            ->assertForbidden();
    }

    public function test_list_shows_only_slots_in_selected_week_and_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Satu']);
        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:00:00',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->assertSee('Matematika')
            ->assertSee('Guru Satu')
            ->assertSee('08:00')
            ->assertDontSee('2026-09-21');
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }
}
