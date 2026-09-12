<?php

namespace Tests\Feature\Admin;

use App\Jadwal;
use App\Kelas;
use App\Livewire\Admin\JadwalMingguan;
use App\Mapel;
use App\Markas;
use App\Pendidik;
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

    public function test_admin_can_create_slot_in_own_markas(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:00')
            ->set('jam_selesai', '09:30')
            ->call('simpan')
            ->assertHasNoErrors();

        $row = Jadwal::firstOrFail();
        $this->assertSame($admin->id, (int) $row->staf_id);
        $this->assertSame('2026-09-14 08:00:00', $row->mulai->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-14 09:30:00', $row->selesai->format('Y-m-d H:i:s'));
    }

    public function test_overlapping_slot_is_rejected(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:30')
            ->set('jam_selesai', '09:30')
            ->call('simpan')
            ->assertHasErrors(['jam_mulai']);
    }

    public function test_admin_cannot_create_slot_for_other_markas_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $guru->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:00')
            ->set('jam_selesai', '09:00')
            ->call('simpan')
            ->assertHasErrors(['kelas_id']);
    }

    private function ownMarkasFixture(): array
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $guru->id, 'markas_id' => $genteng->id]);

        return [$admin, $kelas, $mapel, $guru];
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }
}
