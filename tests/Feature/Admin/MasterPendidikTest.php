<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MasterPendidik;
use App\Markas;
use App\Pendidik;
use App\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterPendidikTest extends TestCase
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

    public function test_creating_pendidik_uses_default_password(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendidik::class)
            ->set('nama', 'Guru Baru')
            ->set('email', 'guru@example.com')
            ->set('markas_id', (string) $markas->id)
            ->call('tambah')
            ->assertHasNoErrors();

        $user = User::where('email', 'guru@example.com')->first();
        $this->assertSame(3, (int) $user->role_id);
        $this->assertTrue(Hash::check(Pendidik::DEFAULT_PASSWORD, $user->password));
        $this->assertDatabaseHas('adm_pendidik', [
            'pendidik_id' => $user->id,
            'markas_id' => $markas->id,
            'mapel_id' => 10,
        ]);
    }

    public function test_non_super_only_sees_pendidik_in_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $a = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Genteng']);
        $b = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Jember']);
        Pendidik::create(['pendidik_id' => $a->id, 'mapel_id' => 10, 'markas_id' => $genteng->id]);
        Pendidik::create(['pendidik_id' => $b->id, 'mapel_id' => 10, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->assertSee('Guru Genteng')
            ->assertDontSee('Guru Jember');
    }

    public function test_non_super_cannot_assign_foreign_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->set('nama', 'Guru X')
            ->set('email', 'gurux@example.com')
            ->set('markas_id', (string) $jember->id)
            ->call('tambah')
            ->assertHasErrors(['markas_id']);
    }

    public function test_non_super_with_one_markas_gets_it_prefilled(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->assertSet('markas_id', (string) $genteng->id);
    }

    public function test_non_super_cannot_see_or_delete_pendidik_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $user = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $user->id, 'mapel_id' => 10, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->call('lihat', $user->id)
            ->assertForbidden();

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->call('hapus', $user->id)
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_non_super_can_delete_pendidik_in_own_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $user = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $user->id, 'mapel_id' => 10, 'markas_id' => $genteng->id]);

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->call('hapus', $user->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_super_cannot_use_legacy_pendidik_routes(): void
    {
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $user = User::factory()->create(['role_id' => 3]);

        $this->actingAs($admin)
            ->post(route('super.penggunapendidik.tambah'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('super.penggunapendidik.lihat', $user->id))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('super.penggunapendidik.hapus', $user->id))
            ->assertForbidden();
    }

    public function test_non_admin_cannot_mount_master_pendidik_directly(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 3]))
            ->test(MasterPendidik::class)
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
