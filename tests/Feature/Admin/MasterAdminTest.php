<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MasterAdmin;
use App\Markas;
use App\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterAdminTest extends TestCase
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

    public function test_non_super_is_forbidden_on_master_admin(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.pengguna.admin'))
            ->assertForbidden();
    }

    public function test_creating_non_super_admin_requires_markas(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->set('nama', 'Staf Satu')
            ->set('email', 'staf1@example.com')
            ->set('password', 'secret123')
            ->set('is_super_admin', false)
            ->set('markas_id', '')
            ->call('tambah')
            ->assertHasErrors(['markas_id']);
    }

    public function test_creating_super_admin_without_markas_succeeds(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->set('nama', 'Super Dua')
            ->set('email', 'super2@example.com')
            ->set('password', 'secret123')
            ->set('is_super_admin', true)
            ->set('markas_id', '')
            ->call('tambah')
            ->assertHasNoErrors();

        $created = User::where('email', 'super2@example.com')->firstOrFail();
        $this->assertTrue($created->isSuperAdmin());
        $this->assertSame(2, (int) $created->role_id);
        $this->assertSame([], $created->markasIds());
    }

    public function test_creating_non_super_admin_attaches_markas(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->set('nama', 'Staf Dua')
            ->set('email', 'staf2@example.com')
            ->set('password', 'secret123')
            ->set('is_super_admin', false)
            ->set('markas_id', (string) $markas->id)
            ->call('tambah')
            ->assertHasNoErrors();

        $created = User::where('email', 'staf2@example.com')->firstOrFail();
        $this->assertFalse($created->isSuperAdmin());
        $this->assertEquals([$markas->id], $created->markasIds());
        $this->assertTrue(Hash::check('secret123', $created->password));
    }

    public function test_cannot_delete_own_admin_account(): void
    {
        $super = $this->superAdmin();

        Livewire::actingAs($super)
            ->test(MasterAdmin::class)
            ->call('hapus', $super->id)
            ->assertHasErrors(['hapus']);

        $this->assertDatabaseHas('users', ['id' => $super->id]);
    }

    public function test_search_filters_admins_by_name(): void
    {
        User::factory()->create([
            'nama' => 'Admin Genteng',
            'email' => 'genteng@example.com',
            'role_id' => 2,
        ]);
        User::factory()->create([
            'nama' => 'Admin Jember',
            'email' => 'jember@example.com',
            'role_id' => 2,
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->set('cari', 'Genteng')
            ->assertSee('Admin Genteng')
            ->assertDontSee('Admin Jember');
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
