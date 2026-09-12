<?php

namespace Tests\Feature\Admin;

use App\Kelas;
use App\Livewire\Admin\MasterMarkas;
use App\Markas;
use App\Pelajar;
use App\Pendidik;
use App\User;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterMarkasTest extends TestCase
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

    public function test_non_super_is_forbidden_on_master_markas(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.master.markas'))
            ->assertForbidden();
    }

    public function test_non_super_cannot_mount_master_markas_directly(): void
    {
        Livewire::actingAs($this->markasAdmin())
            ->test(MasterMarkas::class)
            ->assertForbidden();
    }

    public function test_super_page_renders_the_livewire_master(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.master.markas'))
            ->assertOk()
            ->assertSeeLivewire(MasterMarkas::class);
    }

    public function test_tambah_creates_markas(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->set('nama', 'Situbondo')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('adm_markas', ['markas' => 'Situbondo']);
    }

    public function test_nama_must_be_unique(): void
    {
        Markas::create(['markas' => 'Genteng']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->set('nama', 'Genteng')
            ->call('simpan')
            ->assertHasErrors(['nama']);
    }

    public function test_simpan_updates_existing_markas(): void
    {
        $markas = Markas::create(['markas' => 'Genteng Lama']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->call('ubah', $markas->id)
            ->set('nama', 'Genteng')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame('Genteng', $markas->fresh()->markas);
    }

    public function test_hapus_unused_markas(): void
    {
        $markas = Markas::create(['markas' => 'Kosong']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->call('hapus', $markas->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('adm_markas', ['id' => $markas->id]);
    }

    public function test_hapus_blocked_when_kelas_exists(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        Kelas::create(['nama' => 'A - Genteng', 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->call('hapus', $markas->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('adm_markas', ['id' => $markas->id]);
    }

    public function test_hapus_blocked_when_pelajar_exists(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $user = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $user->id, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->call('hapus', $markas->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('adm_markas', ['id' => $markas->id]);
    }

    public function test_hapus_blocked_when_pendidik_exists(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $user = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $user->id, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->call('hapus', $markas->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('adm_markas', ['id' => $markas->id]);
    }

    public function test_hapus_blocked_when_admin_assigned(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($markas->id);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->call('hapus', $markas->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('adm_markas', ['id' => $markas->id]);
    }

    public function test_search_filters_markas_by_name(): void
    {
        Markas::create(['markas' => 'Genteng']);
        Markas::create(['markas' => 'Jember']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
            ->set('cari', 'Genteng')
            ->assertSee('Genteng')
            ->assertDontSee('Jember');
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Markas::create(['markas' => "Markas {$i}"]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterMarkas::class)
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
