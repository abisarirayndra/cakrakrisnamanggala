<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\MasterPendaftar;
use App\Markas;
use App\Pelajar;
use App\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class MasterPendaftarTest extends TestCase
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
        $app['config']->set('filesystems.disks.pelajar_foto', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/disks/pelajar_foto'),
            'url' => '/img/pelajar',
            'visibility' => 'public',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpAdminMasterSchema();
    }

    public function test_pendaftar_page_renders_the_livewire_master(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.pengguna.pendaftar'))
            ->assertOk()
            ->assertSeeLivewire(MasterPendaftar::class);
    }

    public function test_non_super_only_sees_pendaftar_in_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        $a = User::factory()->create(['role_id' => 5, 'nama' => 'Daftar Genteng']);
        $b = User::factory()->create(['role_id' => 5, 'nama' => 'Daftar Jember']);
        Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
        Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPendaftar::class)
            ->assertSee('Daftar Genteng')
            ->assertDontSee('Daftar Jember');
    }

    public function test_super_sees_pendaftar_from_every_markas_including_unassigned(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $a = User::factory()->create(['role_id' => 5, 'nama' => 'Daftar Genteng']);
        $b = User::factory()->create(['role_id' => 5, 'nama' => 'Daftar Jember']);
        $c = User::factory()->create(['role_id' => 5, 'nama' => 'Belum Ada Markas']);
        Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
        Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);
        Pelajar::create(['pelajar_id' => $c->id, 'markas_id' => null]);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendaftar::class)
            ->assertSee('Daftar Genteng')
            ->assertSee('Daftar Jember')
            ->assertSee('Belum Ada Markas');
    }

    public function test_non_super_cannot_lihat_pendaftar_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pendaftar = User::factory()->create(['role_id' => 5]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(MasterPendaftar::class)
            ->call('lihat', $pendaftar->id)
            ->assertForbidden();
    }

    public function test_migrasi_assigns_kelas_and_promotes_to_pelajar(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pendaftar = User::factory()->create(['role_id' => 5, 'nomor_registrasi' => null]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id]);
        $kelasId = $this->insertKelas('Reguler Genteng', $genteng->id);

        Livewire::actingAs($admin)
            ->test(MasterPendaftar::class)
            ->call('lihat', $pendaftar->id)
            ->set('kelas_id', (string) $kelasId)
            ->call('migrasi')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.pengguna.pelajar'));

        $pendaftar->refresh();
        $this->assertSame(4, (int) $pendaftar->role_id);
        $this->assertSame($kelasId, (int) $pendaftar->kelas_id);
        $this->assertNotNull($pendaftar->nomor_registrasi);
        $this->assertSame(6, strlen($pendaftar->nomor_registrasi));
    }

    public function test_super_terima_sees_kelas_from_every_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $pendaftar = User::factory()->create(['role_id' => 5]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id]);
        $this->insertKelas('Reguler Genteng', $genteng->id);
        $this->insertKelas('Reguler Jember', $jember->id);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendaftar::class)
            ->call('lihat', $pendaftar->id)
            ->call('bukaMigrasi')
            ->assertSee('Reguler Genteng')
            ->assertSee('Reguler Jember');
    }

    public function test_admin_terima_only_sees_kelas_in_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pendaftar = User::factory()->create(['role_id' => 5]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id]);
        $this->insertKelas('Reguler Genteng', $genteng->id);
        $this->insertKelas('Reguler Jember', $jember->id);

        Livewire::actingAs($admin)
            ->test(MasterPendaftar::class)
            ->call('lihat', $pendaftar->id)
            ->call('bukaMigrasi')
            ->assertSee('Reguler Genteng')
            ->assertDontSee('Reguler Jember');
    }

    public function test_super_can_migrasi_to_kelas_in_another_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $pendaftar = User::factory()->create(['role_id' => 5, 'nomor_registrasi' => null]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id]);
        $jemberKelasId = $this->insertKelas('Reguler Jember', $jember->id);

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendaftar::class)
            ->call('lihat', $pendaftar->id)
            ->set('kelas_id', (string) $jemberKelasId)
            ->call('migrasi')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.pengguna.pelajar'));

        $this->assertSame(4, (int) $pendaftar->fresh()->role_id);
        $this->assertSame($jemberKelasId, (int) $pendaftar->fresh()->kelas_id);
    }

    public function test_non_super_cannot_migrasi_to_foreign_kelas(): void

    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pendaftar = User::factory()->create(['role_id' => 5]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id]);
        $foreignKelasId = $this->insertKelas('Reguler Jember', $jember->id);

        Livewire::actingAs($admin)
            ->test(MasterPendaftar::class)
            ->call('lihat', $pendaftar->id)
            ->set('kelas_id', (string) $foreignKelasId)
            ->call('migrasi')
            ->assertHasErrors(['kelas_id']);

        $this->assertSame(5, (int) $pendaftar->fresh()->role_id);
    }

    public function test_hapus_removes_pendaftar_account(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $pendaftar = User::factory()->create(['role_id' => 5]);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id, 'foto' => 'gone.jpg']);

        Livewire::actingAs($admin)
            ->test(MasterPendaftar::class)
            ->call('hapus', $pendaftar->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $pendaftar->id]);
        $this->assertDatabaseMissing('adm_pelajars', ['pelajar_id' => $pendaftar->id]);
    }

    public function test_non_admin_cannot_mount_master_pendaftar_directly(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(MasterPendaftar::class)
            ->assertForbidden();
    }

    public function test_legacy_staf_admin_list_redirects_to_new_page(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('staf-admin.penggunapendaftar'))
            ->assertRedirect(route('admin.pengguna.pendaftar'));
    }

    public function test_list_pagination_uses_bootstrap_markup(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);

        for ($i = 1; $i <= 11; $i++) {
            $user = User::factory()->create(['role_id' => 5, 'nama' => "Pendaftar {$i}"]);
            Pelajar::create(['pelajar_id' => $user->id, 'markas_id' => $markas->id]);
        }

        Livewire::actingAs($this->superAdmin())
            ->test(MasterPendaftar::class)
            ->assertSeeHtml('<ul class="pagination">')
            ->assertDontSeeHtml('inline-flex items-center');
    }

    private function insertKelas(string $nama, int $markasId): int
    {
        return (int) DB::table('kelas')->insertGetId([
            'nama' => $nama,
            'markas_id' => $markasId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }
}
