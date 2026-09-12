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

    public function test_non_super_cannot_mount_master_admin_directly(): void
    {
        Livewire::actingAs($this->markasAdmin())
            ->test(MasterAdmin::class)
            ->assertForbidden();
    }

    public function test_lihat_shows_edit_button(): void
    {
        $admin = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
            'nama' => 'Staf Lihat',
            'email' => 'lihat@example.com',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->call('lihat', $admin->id)
            ->assertSee('Staf Lihat')
            ->assertSee('lihat@example.com')
            ->assertSee('Edit data');
    }

    public function test_edit_reuses_the_create_form(): void
    {
        $admin = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
            'nama' => 'Staf Form',
            'email' => 'form@example.com',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->call('edit', $admin->id)
            ->assertSet('nama', 'Staf Form')
            ->assertSet('email', 'form@example.com')
            ->assertSee('Daftar admin')
            ->assertSee('Ubah admin')
            ->assertSee('Simpan perubahan')
            ->assertSee('Batal')
            ->assertDontSee('Tambah admin');
    }

    public function test_can_update_admin_without_changing_password(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
            'nama' => 'Staf Lama',
            'email' => 'lama@example.com',
            'password' => Hash::make('awal123'),
        ]);
        $admin->markas()->attach($genteng->id);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->call('edit', $admin->id)
            ->set('nama', 'Staf Baru')
            ->set('email', 'baru@example.com')
            ->set('password', '')
            ->set('is_super_admin', false)
            ->set('markas_id', (string) $jember->id)
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Staf Baru')
            ->assertSee('Daftar admin')
            ->assertSee('Tambah admin');

        $admin->refresh();
        $this->assertSame('Staf Baru', $admin->nama);
        $this->assertSame('baru@example.com', $admin->email);
        $this->assertTrue(Hash::check('awal123', $admin->password));
        $this->assertEquals([$jember->id], $admin->markasIds());
    }

    public function test_updating_password_hashes_the_new_value(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
            'password' => Hash::make('awal123'),
        ]);
        $admin->markas()->attach($genteng->id);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->call('edit', $admin->id)
            ->set('password', 'baru456')
            ->set('markas_id', (string) $genteng->id)
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('baru456', $admin->fresh()->password));
    }

    public function test_updating_non_super_still_requires_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
        ]);
        $admin->markas()->attach($genteng->id);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->call('edit', $admin->id)
            ->set('is_super_admin', false)
            ->set('markas_id', '')
            ->call('simpan')
            ->assertHasErrors(['markas_id']);
    }

    public function test_cannot_remove_own_superadmin_flag(): void
    {
        $super = $this->superAdmin();

        Livewire::actingAs($super)
            ->test(MasterAdmin::class)
            ->call('edit', $super->id)
            ->set('is_super_admin', false)
            ->call('simpan')
            ->assertHasErrors(['is_super_admin']);

        $this->assertTrue($super->fresh()->isSuperAdmin());
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

    public function test_creating_non_super_admin_rejects_invalid_markas(): void
    {
        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->set('nama', 'Staf Invalid')
            ->set('email', 'invalid@example.com')
            ->set('password', 'secret123')
            ->set('is_super_admin', false)
            ->set('markas_id', '9999')
            ->call('tambah')
            ->assertHasErrors(['markas_id']);

        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.com']);
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

    public function test_list_is_sorted_by_nama_ascending(): void
    {
        User::factory()->create([
            'role_id' => 2,
            'nama' => 'Alpha Admin',
            'email' => 'alpha-sort@example.com',
        ]);
        User::factory()->create([
            'role_id' => 2,
            'nama' => 'Zeta Admin',
            'email' => 'zeta-sort@example.com',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
            ->assertSeeInOrder(['Alpha Admin', 'Zeta Admin']);
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            User::factory()->create([
                'role_id' => 2,
                'nama' => "Admin {$i}",
                'email' => "admin{$i}@example.com",
            ]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterAdmin::class)
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
