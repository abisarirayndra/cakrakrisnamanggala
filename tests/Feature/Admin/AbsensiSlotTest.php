<?php

namespace Tests\Feature\Admin;

use App\Kelas;
use App\Markas;
use App\Pelajar;
use App\Support\AdminVisibility;
use App\User;
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
}
