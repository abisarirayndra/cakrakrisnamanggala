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

    public function test_non_super_cannot_suspend_or_delete(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('suspend', $pelajar->id)
            ->assertForbidden();

        Livewire::actingAs($admin)
            ->test(MasterPelajar::class)
            ->call('hapus', $pelajar->id)
            ->assertForbidden();

        $this->assertSame(4, (int) $pelajar->fresh()->role_id);
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

    public function test_super_cannot_open_inactive_pelajar_row(): void
    {
        $pelajar = User::factory()->create(['role_id' => 6]);
        Pelajar::create(['pelajar_id' => $pelajar->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPelajar::class)
            ->call('lihat', $pelajar->id)
            ->assertNotFound();
    }

    public function test_non_admin_cannot_mount_master_pelajar_directly(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(MasterPelajar::class)
            ->assertForbidden();
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }
}
