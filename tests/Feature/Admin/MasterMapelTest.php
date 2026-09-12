<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MasterMapel;
use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterMapelTest extends TestCase
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

    public function test_non_super_is_forbidden_on_master_mapel(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.master.mapel'))
            ->assertForbidden();
    }

    public function test_non_super_cannot_mount_master_mapel_directly(): void
    {
        Livewire::actingAs($this->markasAdmin())
            ->test(MasterMapel::class)
            ->assertForbidden();
    }

    public function test_super_page_renders_the_livewire_master(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.master.mapel'))
            ->assertOk()
            ->assertSeeLivewire(MasterMapel::class);
    }

    public function test_tambah_creates_mapel(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->set('nama', 'Astronomi')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mapels', ['mapel' => 'Astronomi']);
    }

    public function test_nama_must_be_unique(): void
    {
        Mapel::create(['mapel' => 'Matematika']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->set('nama', 'Matematika')
            ->call('simpan')
            ->assertHasErrors(['nama']);
    }

    public function test_simpan_updates_existing_mapel(): void
    {
        $mapel = Mapel::create(['mapel' => 'Fisika Lama']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->call('ubah', $mapel->id)
            ->set('nama', 'Fisika')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame('Fisika', $mapel->fresh()->mapel);
    }

    public function test_hapus_unused_mapel(): void
    {
        $mapel = Mapel::create(['mapel' => 'Kosong']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->call('hapus', $mapel->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('mapels', ['id' => $mapel->id]);
    }

    public function test_hapus_blocked_when_pendidik_exists(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $user = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $user->id, 'mapel_id' => $mapel->id, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->call('hapus', $mapel->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('mapels', ['id' => $mapel->id]);
    }

    public function test_hapus_blocked_when_tes_exists(): void
    {
        $mapel = Mapel::create(['mapel' => 'SKD']);
        DB::table('dn_tes')->insert(['mapel_id' => $mapel->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->call('hapus', $mapel->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('mapels', ['id' => $mapel->id]);
    }

    public function test_hapus_blocked_when_tema_exists(): void
    {
        $mapel = Mapel::create(['mapel' => 'Tema Mapel']);
        DB::table('temas')->insert(['mapel_id' => $mapel->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->call('hapus', $mapel->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('mapels', ['id' => $mapel->id]);
    }

    public function test_hapus_blocked_when_jadwal_exists(): void
    {
        $mapel = Mapel::create(['mapel' => 'Jadwal Mapel']);
        DB::table('adm_jadwal')->insert(['mapel_id' => $mapel->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->call('hapus', $mapel->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('mapels', ['id' => $mapel->id]);
    }

    public function test_search_filters_mapel_by_name(): void
    {
        Mapel::create(['mapel' => 'Matematika']);
        Mapel::create(['mapel' => 'Bahasa Inggris']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->set('cari', 'Matematika')
            ->assertSee('Matematika')
            ->assertDontSee('Bahasa Inggris');
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Mapel::create(['mapel' => "Mapel {$i}"]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMapel::class)
            ->assertSeeHtml('<ul class="pagination">')
            ->assertDontSeeHtml('inline-flex items-center');
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
