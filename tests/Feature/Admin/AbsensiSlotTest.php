<?php

namespace Tests\Feature\Admin;

use App\Jadwal;
use App\Kelas;
use App\Livewire\Admin\AbsensiSlot;
use App\Mapel;
use App\Markas;
use App\Pelajar;
use App\Pendidik;
use App\Support\AdminVisibility;
use App\User;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class AbsensiSlotTest extends TestCase
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

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    public function test_pelajar_for_kelas_is_only_aktif_in_that_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $ka = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $kb = Kelas::create(['nama' => 'B', 'markas_id' => $genteng->id]);
        $mine = User::factory()->create(['role_id' => 4, 'kelas_id' => $ka->id, 'nama' => 'Siswa A']);
        User::factory()->create(['role_id' => 4, 'kelas_id' => $kb->id, 'nama' => 'Siswa B']);
        User::factory()->create(['role_id' => 6, 'kelas_id' => $ka->id, 'nama' => 'Suspended']);
        Pelajar::create(['pelajar_id' => $mine->id, 'markas_id' => $genteng->id]);

        $ids = AdminVisibility::pelajarForKelas($ka)->pluck('id')->all();
        $this->assertEquals([$mine->id], $ids);
    }

    public function test_non_admin_cannot_mount_absensi_slot(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(AbsensiSlot::class)
            ->assertForbidden();
    }

    public function test_list_shows_only_todays_slots_for_selected_kelas(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $fisika = Mapel::create(['mapel' => 'Fisika']);
        $today = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $fisika->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-15 08:00:00',
            'selesai' => '2026-09-15 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->assertSee('Matematika')
            ->assertSee('08:00')
            ->assertDontSee('Fisika');
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
}
