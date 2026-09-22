<?php

namespace Tests\Feature\Admin;

use App\BankPaket;
use App\CatJadwal;
use App\Jadwal;
use App\Kelas;
use App\Mapel;
use App\Markas;
use App\Pelajar;
use App\Pendidik;
use App\Support\BankSoalTipe;
use App\User;
use Carbon\Carbon;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class AdminBerandaTest extends TestCase
{
    use CreatesAdminMasterSchema;
    use CreatesCatBankSchema;

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
        $this->setUpCatBankSchema();
        Carbon::setTestNow('2026-09-21 08:30:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_beranda_shows_empty_dashboard_counts(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Pendaftar')
            ->assertSee('Pelajar')
            ->assertSee('Jadwal hari ini')
            ->assertSee('CAT hari ini')
            ->assertSee('Belum ada pendaftar')
            ->assertSee('Belum ada pelajar')
            ->assertSee('Tidak ada jadwal hari ini')
            ->assertSee('Tidak ada CAT hari ini')
            ->assertDontSee('Pilih data yang ingin Anda kelola');
    }

    public function test_beranda_lists_pendaftar_pelajar_today_schedule_and_cat(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $kelas = Kelas::create(['nama' => 'Reguler A', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Satu']);
        Pendidik::create(['pendidik_id' => $guru->id, 'mapel_id' => $mapel->id, 'markas_id' => $genteng->id]);

        $pendaftar = User::factory()->create(['role_id' => 5, 'nama' => 'Ani Daftar']);
        Pelajar::create(['pelajar_id' => $pendaftar->id, 'markas_id' => $genteng->id]);

        $pelajar = User::factory()->create(['role_id' => 4, 'nama' => 'Budi Siswa', 'kelas_id' => $kelas->id]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-22 08:00:00',
            'selesai' => '2026-09-22 09:00:00',
        ]);

        $paket = BankPaket::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'nama' => 'Bank UTS',
            'tipe' => BankSoalTipe::TUNGGAL,
            'bentuk' => 'biasa',
        ]);
        $hariIni = CatJadwal::create([
            'admin_id' => 1,
            'nama' => 'UTS Hari Ini',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 10:00:00',
            'token' => 'HARI01',
        ]);
        $hariIni->pasangBanks([$paket->id]);
        $besok = CatJadwal::create([
            'admin_id' => 1,
            'nama' => 'UTS Besok',
            'mulai' => '2026-09-22 08:00:00',
            'selesai' => '2026-09-22 10:00:00',
            'token' => 'BESOK1',
        ]);
        $besok->pasangBanks([$paket->id]);

        $this->actingAs($this->superAdmin())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Ani Daftar')
            ->assertSee('Genteng')
            ->assertSee('Reguler A')
            ->assertSee('Matematika')
            ->assertSee('Guru Satu')
            ->assertSee('08:00–09:00')
            ->assertSee('UTS Hari Ini')
            ->assertSee('HARI01')
            ->assertSee('Berlangsung')
            ->assertSee('Bank UTS')
            ->assertSee(route('admin.cat.jadwal.skor', $hariIni, false), false)
            ->assertDontSee('UTS Besok')
            ->assertDontSee('BESOK1');
    }

    public function test_markas_admin_only_sees_own_markas_dashboard(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false, 'nama' => 'Admin Genteng']);
        $admin->markas()->attach($genteng->id);

        $pendaftarLain = User::factory()->create(['role_id' => 5, 'nama' => 'Daftar Jember']);
        Pelajar::create(['pelajar_id' => $pendaftarLain->id, 'markas_id' => $jember->id]);

        $kelasLain = Kelas::create(['nama' => 'Kelas Jember', 'markas_id' => $jember->id]);
        $pelajarLain = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Jember', 'kelas_id' => $kelasLain->id]);
        Pelajar::create(['pelajar_id' => $pelajarLain->id, 'markas_id' => $jember->id]);

        $this->actingAs($admin)
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Admin Genteng')
            ->assertDontSee('Daftar Jember')
            ->assertDontSee('Kelas Jember')
            ->assertSee('Belum ada pendaftar')
            ->assertSee('Belum ada pelajar');
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
            'nama' => 'Super Uji',
        ]);
    }
}
