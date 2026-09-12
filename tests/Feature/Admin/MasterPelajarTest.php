<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MasterPelajar;
use App\Markas;
use App\Pelajar;
use App\User;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterPelajarTest extends TestCase
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

    public function test_non_super_only_sees_pelajar_in_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        $a = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Genteng']);
        $b = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Jember']);
        Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
        Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->assertSee('Ada Di Genteng')
            ->assertDontSee('Ada Di Jember');
    }

    public function test_super_sees_pelajar_from_every_markas_including_unassigned(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $a = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Genteng']);
        $b = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Jember']);
        $c = User::factory()->create(['role_id' => 4, 'nama' => 'Belum Ada Markas']);
        Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
        Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);
        Pelajar::create(['pelajar_id' => $c->id, 'markas_id' => null]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->assertSee('Ada Di Genteng')
            ->assertSee('Ada Di Jember')
            ->assertSee('Belum Ada Markas');
    }

    public function test_admin_can_suspend_pelajar_in_own_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4, 'nama' => 'Pelajar Genteng']);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->assertSeeHtml('>Suspend</button>')
            ->assertSeeHtml('>Hapus</button>')
            ->call('suspend', $pelajar->id)
            ->assertHasNoErrors();

        $this->assertSame(6, (int) $pelajar->fresh()->role_id);
    }

    public function test_admin_cannot_suspend_pelajar_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('suspend', $pelajar->id)
            ->assertForbidden();

        $this->assertSame(4, (int) $pelajar->fresh()->role_id);
    }

    public function test_admin_can_hapus_pelajar_in_own_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('hapus', $pelajar->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $pelajar->id]);
    }

    public function test_admin_cannot_hapus_pelajar_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('hapus', $pelajar->id)
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $pelajar->id]);
    }

    public function test_super_can_suspend_pelajar(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->call('suspend', $pelajar->id)
            ->assertHasNoErrors();

        $this->assertSame(6, (int) $pelajar->fresh()->role_id);
    }

    public function test_non_super_can_edit_biodata_in_own_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        $row = Pelajar::create([
            'pelajar_id' => $pelajar->id,
            'markas_id' => $genteng->id,
            'nik' => '111',
            'foto' => 'tetap.jpg',
        ]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('edit', $pelajar->id)
            ->set('nik', '999')
            ->set('markas_id', (string) $genteng->id)
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame('999', $row->fresh()->nik);
        $this->assertSame('tetap.jpg', $row->fresh()->foto);
    }

    public function test_non_super_cannot_edit_pelajar_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('edit', $pelajar->id)
            ->assertForbidden();
    }

    public function test_non_super_cannot_open_legacy_edit_url(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        $this->actingAs($admin)
            ->get(route('super.penggunapelajar.edit', $pelajar->id))
            ->assertForbidden();
    }

    public function test_non_super_cannot_open_legacy_suspend_url(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        $this->actingAs($admin)
            ->get(route('super.penggunapelajar.suspend', $pelajar->id))
            ->assertForbidden();

        $this->assertSame(4, (int) $pelajar->fresh()->role_id);
    }

    public function test_non_super_cannot_open_legacy_hapus_url(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        $this->actingAs($admin)
            ->get(route('super.penggunapelajar.hapus', $pelajar->id))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $pelajar->id]);
    }

    public function test_list_shows_active_and_suspended_tabs(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->assertSee('Pelajar aktif')
            ->assertSee('Suspended');
    }

    public function test_active_tab_hides_suspended_pelajar(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $aktif = User::factory()->create(['role_id' => 4, 'nama' => 'Masih Aktif']);
        $suspend = User::factory()->create(['role_id' => 6, 'nama' => 'Sudah Suspend']);
        Pelajar::create(['pelajar_id' => $aktif->id, 'markas_id' => $markas->id]);
        Pelajar::create(['pelajar_id' => $suspend->id, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->assertSee('Masih Aktif')
            ->assertDontSee('Sudah Suspend')
            ->assertDontSee('Unsuspend');
    }

    public function test_suspended_tab_shows_only_suspended_and_unsuspend(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $aktif = User::factory()->create(['role_id' => 4, 'nama' => 'Masih Aktif']);
        $suspend = User::factory()->create(['role_id' => 6, 'nama' => 'Sudah Suspend']);
        Pelajar::create(['pelajar_id' => $aktif->id, 'markas_id' => $markas->id]);
        Pelajar::create(['pelajar_id' => $suspend->id, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->call('pilihTab', 'suspended')
            ->assertSee('Sudah Suspend')
            ->assertDontSee('Masih Aktif')
            ->assertSee('Unsuspend');
    }

    public function test_admin_sees_suspended_only_in_own_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        $a = User::factory()->create(['role_id' => 6, 'nama' => 'Genteng Suspend']);
        $b = User::factory()->create(['role_id' => 6, 'nama' => 'Jember Suspend']);
        Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
        Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('pilihTab', 'suspended')
            ->assertSee('Genteng Suspend')
            ->assertDontSee('Jember Suspend');
    }

    public function test_admin_can_unsuspend_pelajar_in_own_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 6, 'nama' => 'Alumni Genteng']);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('unsuspend', $pelajar->id)
            ->assertHasNoErrors();

        $this->assertSame(4, (int) $pelajar->fresh()->role_id);
    }

    public function test_admin_cannot_unsuspend_pelajar_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 6]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('unsuspend', $pelajar->id)
            ->assertForbidden();

        $this->assertSame(6, (int) $pelajar->fresh()->role_id);
    }

    public function test_super_can_open_suspended_pelajar(): void
    {
        $pelajar = User::factory()->create(['role_id' => 6, 'nama' => 'Alumni Suspend']);
        Pelajar::create(['pelajar_id' => $pelajar->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->call('lihat', $pelajar->id)
            ->assertSee('Alumni Suspend')
            ->assertSee('Unsuspend');
    }

    public function test_non_admin_cannot_mount_master_pelajar_directly(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(MasterPelajar::class)
            ->assertForbidden();
    }

    public function test_list_is_sorted_by_nama_ascending(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $alpha = User::factory()->create(['role_id' => 4, 'nama' => 'Alpha Pelajar']);
        $zeta = User::factory()->create(['role_id' => 4, 'nama' => 'Zeta Pelajar']);
        Pelajar::create(['pelajar_id' => $alpha->id, 'markas_id' => $markas->id]);
        Pelajar::create(['pelajar_id' => $zeta->id, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->assertSeeInOrder(['Alpha Pelajar', 'Zeta Pelajar']);
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);

        for ($i = 1; $i <= 11; $i++) {
            $user = User::factory()->create(['role_id' => 4, 'nama' => "Pelajar {$i}"]);
            Pelajar::create(['pelajar_id' => $user->id, 'markas_id' => $markas->id]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
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
}
