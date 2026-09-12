<?php

namespace Tests\Feature\Admin;

use App\Kelas;
use App\Livewire\Admin\MasterKelas;
use App\Markas;
use App\User;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterKelasTest extends TestCase
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

    public function test_non_super_is_forbidden_on_master_kelas(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.master.kelas'))
            ->assertForbidden();
    }

    public function test_non_super_cannot_mount_master_kelas_directly(): void
    {
        Livewire::actingAs($this->markasAdmin())
            ->test(MasterKelas::class)
            ->assertForbidden();
    }

    public function test_super_page_renders_the_livewire_master(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.master.kelas'))
            ->assertOk()
            ->assertSeeLivewire(MasterKelas::class);
    }

    public function test_tambah_requires_markas(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->set('nama', 'A - Situbondo')
            ->set('markas_id', '')
            ->call('simpan')
            ->assertHasErrors(['markas_id']);
    }

    public function test_tambah_creates_kelas(): void
    {
        $markas = Markas::create(['markas' => 'Situbondo']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->set('nama', 'A - Situbondo')
            ->set('markas_id', (string) $markas->id)
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kelas', [
            'nama' => 'A - Situbondo',
            'markas_id' => $markas->id,
        ]);
    }

    public function test_nama_must_be_unique(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        Kelas::create(['nama' => 'A - Genteng', 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->set('nama', 'A - Genteng')
            ->set('markas_id', (string) $markas->id)
            ->call('simpan')
            ->assertHasErrors(['nama']);
    }

    public function test_simpan_updates_existing_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $kelas = Kelas::create(['nama' => 'A - Genteng', 'markas_id' => $genteng->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->call('ubah', $kelas->id)
            ->set('nama', 'A - Jember')
            ->set('markas_id', (string) $jember->id)
            ->call('simpan')
            ->assertHasNoErrors();

        $kelas->refresh();
        $this->assertSame('A - Jember', $kelas->nama);
        $this->assertSame($jember->id, (int) $kelas->markas_id);
    }

    public function test_hapus_unused_kelas(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $kelas = Kelas::create(['nama' => 'Z - Kosong', 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->call('hapus', $kelas->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }

    public function test_hapus_blocked_when_user_assigned(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $kelas = Kelas::create(['nama' => 'A - Genteng', 'markas_id' => $markas->id]);
        User::factory()->create(['role_id' => 4, 'kelas_id' => $kelas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->call('hapus', $kelas->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('kelas', ['id' => $kelas->id]);
    }

    public function test_search_filters_kelas_by_name(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        Kelas::create(['nama' => 'A - Genteng', 'markas_id' => $genteng->id]);
        Kelas::create(['nama' => 'A - Jember', 'markas_id' => $jember->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->set('cari', 'Genteng')
            ->assertSee('A - Genteng')
            ->assertDontSee('A - Jember');
    }

    public function test_list_shows_markas_name(): void
    {
        $markas = Markas::create(['markas' => 'Banyuwangi']);
        Kelas::create(['nama' => 'A - Banyuwangi', 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
            ->assertSee('A - Banyuwangi')
            ->assertSee('Banyuwangi');
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);

        for ($i = 1; $i <= 11; $i++) {
            Kelas::create(['nama' => "Kelas {$i}", 'markas_id' => $markas->id]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterKelas::class)
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
