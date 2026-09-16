<?php

namespace Tests\Feature\Admin;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Kelas;
use App\Livewire\Admin\HistoriJadwal;
use App\Mapel;
use App\Markas;
use App\Pelajar;
use App\Pendidik;
use App\Support\AbsensiStatus;
use App\User;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class HistoriJadwalTest extends TestCase
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

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    public function test_non_admin_cannot_mount_histori_jadwal(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(HistoriJadwal::class)
            ->assertForbidden();
    }

    public function test_sidebar_folds_jadwal_with_histori_submenu(): void
    {
        Carbon::setTestNow('2026-09-14 10:00:00');
        [$admin] = $this->slotFixture();

        $html = $this->actingAs($admin)
            ->get(route('admin.jadwal.histori'))
            ->assertOk()
            ->assertSee('Histori Jadwal')
            ->assertSee('js-nav-jadwal', false)
            ->assertSee(route('admin.jadwal.histori', absolute: false), false)
            ->getContent();

        $this->assertMatchesRegularExpression('/class="[^"]*js-nav-jadwal[^"]*show/', $html);
        $this->assertStringContainsString('data-nav="histori-jadwal"', $html);
    }

    public function test_list_shows_slots_in_selected_month_and_hides_other_markas(): void
    {
        Carbon::setTestNow('2026-09-14 10:00:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        $fisika = Mapel::create(['mapel' => 'Fisika']);
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $fisika->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-08-14 08:00:00',
            'selesai' => '2026-08-14 09:00:00',
        ]);

        $jember = Markas::create(['markas' => 'Jember']);
        $kelasLain = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $kimia = Mapel::create(['mapel' => 'Kimia']);
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $kimia->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelasLain->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(HistoriJadwal::class)
            ->set('kelas_id', (string) $kelas->id)
            ->assertSee('Matematika')
            ->assertSee('Guru Utama')
            ->assertDontSee('Fisika')
            ->assertDontSee('Kimia')
            ->assertSee('Detail');
    }

    public function test_detail_shows_absensi_and_jurnal(): void
    {
        Carbon::setTestNow('2026-09-14 10:00:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $izin = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Ani Izin',
        ]);
        Pelajar::create(['pelajar_id' => $izin->id, 'markas_id' => $kelas->markas_id]);

        AbsensiPendidik::create([
            'jadwal_id' => $slot->id,
            'pendidik_id' => $guru->id,
            'datang' => '2026-09-14 07:55:00',
            'pulang' => '2026-09-14 09:00:00',
            'status' => AbsensiStatus::ONTIME,
            'jurnal' => 'Pecahan desimal',
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:10:00',
            'pulang' => '2026-09-14 09:00:00',
            'status' => AbsensiStatus::TELAT,
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $izin->id,
            'status' => AbsensiStatus::IZIN,
            'keterangan' => 'Acara keluarga',
        ]);

        Livewire::actingAs($admin)
            ->test(HistoriJadwal::class)
            ->set('kelas_id', (string) $kelas->id)
            ->call('bukaDetail', $slot->id)
            ->assertSee('Pecahan desimal')
            ->assertSee('Budi Santoso')
            ->assertSee('Telat')
            ->assertSee('Ani Izin')
            ->assertSee('Izin')
            ->assertSee('Acara keluarga')
            ->assertSee('Unduh PDF');
    }

    public function test_pdf_streams_parent_report_for_own_slot(): void
    {
        Carbon::setTestNow('2026-09-14 10:00:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        AbsensiPendidik::create([
            'jadwal_id' => $slot->id,
            'pendidik_id' => $guru->id,
            'datang' => '2026-09-14 07:55:00',
            'pulang' => '2026-09-14 09:00:00',
            'status' => AbsensiStatus::ONTIME,
            'jurnal' => 'Pecahan desimal',
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:10:00',
            'status' => AbsensiStatus::TELAT,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.jadwal.histori.pdf', $slot));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('laporan-kehadiran', (string) $response->headers->get('content-disposition'));
    }

    public function test_pdf_hides_other_markas_slot(): void
    {
        Carbon::setTestNow('2026-09-14 10:00:00');
        [$admin] = $this->slotFixture();
        $jember = Markas::create(['markas' => 'Jember']);
        $kelasLain = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $mapel = Mapel::create(['mapel' => 'Kimia']);
        $guru = User::factory()->create(['role_id' => 3]);
        $slotLain = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelasLain->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.jadwal.histori.pdf', $slotLain))
            ->assertNotFound();
    }

    private function slotFixture(): array
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Utama']);
        Pendidik::create(['pendidik_id' => $guru->id, 'markas_id' => $genteng->id]);
        $slot = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        $siswa = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Budi Santoso',
        ]);
        Pelajar::create(['pelajar_id' => $siswa->id, 'markas_id' => $genteng->id]);

        return [$admin, $kelas, $mapel, $guru, $slot, $siswa];
    }
}
