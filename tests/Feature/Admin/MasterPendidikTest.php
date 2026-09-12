<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MasterPendidik;
use App\Mapel;
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

    public function test_lihat_shows_biodata_and_edit_button(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $user = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Detail']);
        Pendidik::create([
            'pendidik_id' => $user->id,
            'markas_id' => $markas->id,
            'nik' => '3510123456780001',
            'nip' => '198012345',
            'tempat_lahir' => 'Banyuwangi',
            'tanggal_lahir' => '1990-01-15',
            'alamat' => 'Jl. Cakra 1',
            'wa' => '081234567890',
            'ibu' => 'Siti',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendidik::class)
            ->call('lihat', $user->id)
            ->assertSee('Guru Detail')
            ->assertSee('3510123456780001')
            ->assertSee('198012345')
            ->assertSee('Banyuwangi')
            ->assertSee('Jl. Cakra 1')
            ->assertSee('081234567890')
            ->assertSee('Siti')
            ->assertSee('Genteng')
            ->assertSee('Edit biodata');
    }

    public function test_can_update_pendidik_biodata(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $user = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Lama', 'email' => 'lama@example.com']);
        $row = Pendidik::create([
            'pendidik_id' => $user->id,
            'markas_id' => $genteng->id,
            'nik' => '111',
            'foto' => 'tetap.jpg',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendidik::class)
            ->call('edit', $user->id)
            ->set('nama', 'Guru Baru')
            ->set('email', 'baru@example.com')
            ->set('nik', '999')
            ->set('nip', '2000')
            ->set('wa', '0812')
            ->set('markas_id', (string) $jember->id)
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Guru Baru')
            ->assertSee('999');

        $user->refresh();
        $row->refresh();
        $this->assertSame('Guru Baru', $user->nama);
        $this->assertSame('baru@example.com', $user->email);
        $this->assertSame('999', $row->nik);
        $this->assertSame('2000', $row->nip);
        $this->assertSame('0812', $row->wa);
        $this->assertSame($jember->id, (int) $row->markas_id);
        $this->assertSame('tetap.jpg', $row->foto);
    }

    public function test_non_super_cannot_edit_pendidik_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $user = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $user->id, 'mapel_id' => 10, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPendidik::class)
            ->call('edit', $user->id)
            ->assertForbidden();
    }

    public function test_list_shows_mapel_name(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $user = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Mapel']);
        Pendidik::create([
            'pendidik_id' => $user->id,
            'mapel_id' => $mapel->id,
            'markas_id' => $markas->id,
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendidik::class)
            ->assertSee('Guru Mapel')
            ->assertSee('Matematika');
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

    public function test_list_is_sorted_by_nama_ascending(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $alpha = User::factory()->create(['role_id' => 3, 'nama' => 'Alpha Guru']);
        $zeta = User::factory()->create(['role_id' => 3, 'nama' => 'Zeta Guru']);
        Pendidik::create(['pendidik_id' => $alpha->id, 'mapel_id' => 10, 'markas_id' => $markas->id]);
        Pendidik::create(['pendidik_id' => $zeta->id, 'mapel_id' => 10, 'markas_id' => $markas->id]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendidik::class)
            ->assertSeeInOrder(['Alpha Guru', 'Zeta Guru']);
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);

        for ($i = 1; $i <= 11; $i++) {
            $user = User::factory()->create(['role_id' => 3, 'nama' => "Guru {$i}"]);
            Pendidik::create(['pendidik_id' => $user->id, 'mapel_id' => 10, 'markas_id' => $markas->id]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendidik::class)
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
