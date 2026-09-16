<?php

namespace Tests\Feature\Admin;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Kelas;
use App\Livewire\Admin\AbsensiSlot;
use App\Mapel;
use App\Markas;
use App\Pelajar;
use App\Pendidik;
use App\Support\AbsensiStatus;
use App\Support\AdminVisibility;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class AbsensiSlotTest extends TestCase
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

    public function test_pelajar_for_kelas_is_only_aktif_in_that_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $ka = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $kb = Kelas::create(['nama' => 'B', 'markas_id' => $genteng->id]);
        $mine = User::factory()->create(['role_id' => 4, 'kelas_id' => $ka->id, 'nama' => 'Siswa A']);
        User::factory()->create(['role_id' => 4, 'kelas_id' => $kb->id, 'nama' => 'Siswa B']);
        User::factory()->create(['role_id' => 6, 'kelas_id' => $ka->id, 'nama' => 'Suspended']);
        Pelajar::create(['pelajar_id' => $mine->id, 'markas_id' => $genteng->id]);

        $ids = AdminVisibility::pelajarForKelas($ka)->pluck('id')->all();
        $this->assertEquals([$mine->id], $ids);
    }

    public function test_non_admin_cannot_mount_absensi_slot(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(AbsensiSlot::class)
            ->assertForbidden();
    }

    public function test_list_shows_only_todays_slots_for_selected_kelas(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $fisika = Mapel::create(['mapel' => 'Fisika']);
        $today = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $fisika->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-15 08:00:00',
            'selesai' => '2026-09-15 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->assertSee('Matematika')
            ->assertSee('08:00')
            ->assertDontSee('Fisika');
    }

    public function test_scan_datang_writes_hadir_for_pelajar(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasNoErrors()
            ->assertSet('token', '')
            ->assertSet('pesan', $siswa->nama.' — Telat');

        $row = AbsensiPelajar::firstOrFail();
        $this->assertSame($siswa->id, (int) $row->pelajar_id);
        $this->assertSame(0, (int) $row->status);
        $this->assertNotNull($row->datang);
        $this->assertNull($row->pulang);
    }

    public function test_second_datang_is_rejected(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:05:00',
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasErrors(['token' => 'Sudah absen datang']);
    }

    public function test_unknown_token_is_rejected(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('token', 'NOPE99')
            ->call('scan')
            ->assertHasErrors(['token' => 'Nomor registrasi tidak ditemukan']);
    }

    public function test_pelajar_other_kelas_is_rejected(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        $lain = Kelas::create(['nama' => 'B', 'markas_id' => $kelas->markas_id]);
        User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $lain->id,
            'nomor_registrasi' => 'XYZ789',
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('token', 'XYZ789')
            ->call('scan')
            ->assertHasErrors(['token' => 'Bukan pelajar kelas ini']);
    }

    public function test_scan_datang_writes_hadir_for_pendidik(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        $guru->update(['nomor_registrasi' => 'GURU01']);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'GURU01')
            ->call('scan')
            ->assertHasNoErrors();

        $row = AbsensiPendidik::firstOrFail();
        $this->assertSame($guru->id, (int) $row->pendidik_id);
        $this->assertSame(0, (int) $row->status);
    }

    public function test_pulang_without_datang_is_rejected(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'pulang')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasErrors(['token' => 'Belum absen datang']);
    }

    public function test_guru_utama_pulang_opens_jurnal_modal(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        AbsensiPendidik::create([
            'jadwal_id' => $slot->id,
            'pendidik_id' => $guru->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'pulang')
            ->assertDontSee('Hanya dipakai saat scan guru utama')
            ->set('token', $guru->nomor_registrasi)
            ->call('scan')
            ->assertHasNoErrors()
            ->assertSet('showJurnalModal', true)
            ->assertSeeHtml('id="jurnal"')
            ->set('jurnal', '')
            ->call('simpanJurnalPulang')
            ->assertHasErrors(['jurnal' => 'Jurnal wajib diisi']);

        $this->assertNull(AbsensiPendidik::first()->pulang);
    }

    public function test_guru_utama_pulang_with_jurnal_succeeds(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        $guru->update(['nomor_registrasi' => 'GURU01', 'nama' => 'Guru Jurnal Utama']);
        AbsensiPendidik::create([
            'jadwal_id' => $slot->id,
            'pendidik_id' => $guru->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'pulang')
            ->set('token', 'GURU01')
            ->call('scan')
            ->assertSet('showJurnalModal', true)
            ->assertSee('Guru Jurnal Utama')
            ->set('jurnal', 'Aljabar linier')
            ->call('simpanJurnalPulang')
            ->assertHasNoErrors()
            ->assertSet('showJurnalModal', false);

        $row = AbsensiPendidik::first();
        $this->assertNotNull($row->pulang);
        $this->assertSame('Aljabar linier', $row->jurnal);
    }

    public function test_other_guru_pulang_without_jurnal_ok(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        $lain = User::factory()->create(['role_id' => 3, 'nomor_registrasi' => 'GURU02']);
        Pendidik::create(['pendidik_id' => $lain->id, 'markas_id' => $kelas->markas_id]);
        AbsensiPendidik::create([
            'jadwal_id' => $slot->id,
            'pendidik_id' => $lain->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'pulang')
            ->set('jurnal', '')
            ->set('token', 'GURU02')
            ->call('scan')
            ->assertHasNoErrors();

        $this->assertNotNull(AbsensiPendidik::where('pendidik_id', $lain->id)->first()->pulang);
    }

    public function test_scan_outside_window_is_rejected(): void
    {
        Carbon::setTestNow('2026-09-14 06:50:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasErrors(['token' => 'Di luar jam absensi']);
    }

    public function test_izin_requires_keterangan(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::IZIN)
            ->set('izin_keterangan', '')
            ->call('simpanIzin')
            ->assertHasErrors(['izin_keterangan' => 'Keterangan wajib untuk izin']);
    }

    public function test_sakit_without_keterangan_ok(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::SAKIT)
            ->set('izin_keterangan', '')
            ->call('simpanIzin')
            ->assertHasNoErrors();

        $row = AbsensiPelajar::first();
        $this->assertSame(AbsensiStatus::SAKIT, (int) $row->status);
        $this->assertNull($row->datang);
    }

    public function test_scan_overrides_izin_to_hadir(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'status' => AbsensiStatus::IZIN,
            'keterangan' => 'keluarga',
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasNoErrors();

        $row = AbsensiPelajar::first();
        $this->assertSame(0, (int) $row->status);
        $this->assertNotNull($row->datang);
    }

    public function test_roster_shows_scanned_pendidik_and_pelajar(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $guru->update(['nama' => 'Guru Roster Utama']);
        $siswa->update(['nama' => 'Siswa Roster Hadir']);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'GURU01')
            ->call('scan')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertSee('Guru Roster Utama')
            ->assertSee('Siswa Roster Hadir')
            ->assertSee('08:30')
            ->assertSee('Guru utama')
            ->assertSee('Telat')
            ->assertDontSee('Belum ada yang datang')
            ->assertDispatched('fokus-token');
    }

    public function test_late_row_stored_as_hadir_still_shows_telat(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Data Lama']);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:30:00',
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->assertSee('Telat')
            ->assertDontSee('Hadir');
    }

    public function test_scan_before_mulai_shows_ontime(): void
    {
        Carbon::setTestNow('2026-09-14 07:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Ontime Roster']);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasNoErrors()
            ->assertSee('Ontime')
            ->assertDontSee('Telat')
            ->assertSet('pesan', 'Siswa Ontime Roster — Ontime');

        $this->assertSame(1, (int) AbsensiPelajar::first()->status);
    }

    public function test_pulang_keeps_telat_status(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:30:00',
            'status' => 0,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'pulang')
            ->set('token', 'ABC123')
            ->call('scan')
            ->assertHasNoErrors();

        $this->assertSame(0, (int) AbsensiPelajar::first()->status);
        $this->assertNotNull(AbsensiPelajar::first()->pulang);
    }

    public function test_scan_form_has_submit_button(): void
    {
        [$admin] = $this->ownMarkasFixture();

        $html = Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->html();

        $scanPos = strpos($html, '>Scan</h2>');
        $scanFormEnd = $scanPos === false ? false : strpos($html, '</form>', $scanPos);
        $submitPos = $scanPos === false ? false : strpos($html, 'type="submit"', $scanPos);

        $this->assertNotFalse($scanPos);
        $this->assertNotFalse($scanFormEnd);
        $this->assertNotFalse($submitPos);
        $this->assertLessThan($scanFormEnd, $submitPos);
        $this->assertStringContainsString('Simpan', substr($html, $scanPos, $scanFormEnd - $scanPos));
    }

    public function test_izin_form_is_outside_scan_card(): void
    {
        [$admin] = $this->ownMarkasFixture();

        $html = Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->html();

        $scanPos = strpos($html, '>Scan</h2>');
        $izinPos = strpos($html, 'Izin / Sakit / Alpa');
        $scanSectionEnd = $scanPos === false ? false : strpos($html, '</section>', $scanPos);

        $this->assertNotFalse($scanPos);
        $this->assertNotFalse($izinPos);
        $this->assertNotFalse($scanSectionEnd);
        $this->assertLessThan(
            $izinPos,
            $scanSectionEnd,
            'Form izin harus kartu terpisah, bukan di dalam kartu scan.'
        );
    }

    public function test_izin_appears_on_roster_immediately(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Izin Roster']);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::SAKIT)
            ->call('simpanIzin')
            ->assertHasNoErrors()
            ->assertSeeHtml('wire:key="absensi-pelajar-'.$siswa->id.'"');
    }

    public function test_datang_and_izin_use_separate_report_cards(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Datang Laporan']);
        $izin = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Siswa Izin Laporan',
        ]);
        Pelajar::create(['pelajar_id' => $izin->id, 'markas_id' => $kelas->markas_id]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:05:00',
            'status' => AbsensiStatus::HADIR,
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $izin->id,
            'status' => AbsensiStatus::IZIN,
            'keterangan' => 'urut',
        ]);

        $html = Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->assertSee('Siswa Datang Laporan')
            ->assertSee('Siswa Izin Laporan')
            ->html();

        $datangPos = strpos($html, '>Datang</h2>');
        $izinLaporanPos = strpos($html, 'id="laporan-izin"');
        $datangNama = strpos($html, 'Siswa Datang Laporan');
        $izinNama = strpos($html, 'Siswa Izin Laporan');

        $this->assertNotFalse($datangPos);
        $this->assertNotFalse($izinLaporanPos);
        $this->assertGreaterThan($datangPos, $izinLaporanPos);
        $this->assertGreaterThan($datangPos, $datangNama);
        $this->assertLessThan($izinLaporanPos, $datangNama);
        $this->assertGreaterThan($izinLaporanPos, $izinNama);
    }

    public function test_other_markas_slot_returns_404_and_writes_nothing(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $jember = Markas::create(['markas' => 'Jember']);
        $kelasLain = Kelas::create(['nama' => 'Z', 'markas_id' => $jember->id]);
        $slotLain = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelasLain->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        try {
            Livewire::actingAs($admin)
                ->test(AbsensiSlot::class)
                ->set('kelas_id', (string) $kelas->id)
                ->set('jadwal_id', (string) $slotLain->id)
                ->set('mode', 'datang')
                ->set('token', 'ABC123')
                ->call('scan');

            $this->fail('Expected other-markas slot to abort with 404.');
        } catch (ModelNotFoundException|NotFoundHttpException $e) {
            if ($e instanceof NotFoundHttpException) {
                $this->assertSame(404, $e->getStatusCode());
            }
        }

        $this->assertSame(0, AbsensiPelajar::count());
        $this->assertSame(0, AbsensiPendidik::count());
        $this->assertDatabaseMissing('adm_absensi_pelajar', ['pelajar_id' => $siswa->id]);
    }

    public function test_pendidik_other_markas_is_rejected(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
        $jember = Markas::create(['markas' => 'Jember']);
        $lain = User::factory()->create([
            'role_id' => 3,
            'nomor_registrasi' => 'JBR001',
            'nama' => 'Guru Jember',
        ]);
        Pendidik::create(['pendidik_id' => $lain->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('mode', 'datang')
            ->set('token', 'JBR001')
            ->call('scan')
            ->assertHasErrors(['token' => 'Bukan pendidik markas ini']);

        $this->assertSame(0, AbsensiPendidik::count());
    }

    public function test_simpan_izin_writes_pendidik_without_datang(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $guru->id)
            ->set('izin_status', (string) AbsensiStatus::SAKIT)
            ->set('izin_keterangan', '')
            ->call('simpanIzin')
            ->assertHasNoErrors();

        $row = AbsensiPendidik::firstOrFail();
        $this->assertSame($guru->id, (int) $row->pendidik_id);
        $this->assertSame(AbsensiStatus::SAKIT, (int) $row->status);
        $this->assertNull($row->datang);
        $this->assertSame(0, AbsensiPelajar::count());
    }

    public function test_whitespace_only_token_is_required(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('token', '   ')
            ->call('scan')
            ->assertHasErrors(['token' => 'required']);

        $this->assertSame(0, AbsensiPelajar::count());
        $this->assertSame(0, AbsensiPendidik::count());
    }

    public function test_izin_rejected_when_already_hadir(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => '2026-09-14 08:05:00',
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::IZIN)
            ->set('izin_keterangan', 'urut')
            ->call('simpanIzin')
            ->assertHasErrors(['izin_user_id' => 'Sudah hadir, ubah lewat scan pulang atau biarkan']);
    }

    public function test_alpa_question_is_in_scan_column(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Belum Absen']);

        $html = Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->html();

        $pertanyaan = 'Siswa yang tidak terabsen dan tidak ada izin, statusnya Alpa?';
        $scanPos = strpos($html, '>Scan</h2>');
        $izinFormPos = $scanPos === false ? false : strpos($html, 'Izin / Sakit / Alpa', $scanPos);
        $alpaPos = strpos($html, $pertanyaan);

        $this->assertNotFalse($scanPos);
        $this->assertNotFalse($izinFormPos);
        $this->assertNotFalse($alpaPos);
        $this->assertGreaterThan($scanPos, $izinFormPos);
        $this->assertGreaterThan($izinFormPos, $alpaPos);
        $this->assertMatchesRegularExpression(
            '/col-lg-4[\s\S]*>Scan<\/h2>[\s\S]*Izin \/ Sakit \/ Alpa[\s\S]*Siswa yang tidak terabsen dan tidak ada izin, statusnya Alpa\?/',
            $html
        );
    }

    public function test_confirm_alpa_marks_unmarked_pelajar_only(): void
    {
        Carbon::setTestNow('2026-09-14 08:30:00');
        [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
        $hadir = $siswa;
        $izin = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Siswa Izin Alpa',
        ]);
        Pelajar::create(['pelajar_id' => $izin->id, 'markas_id' => $kelas->markas_id]);
        $kosong = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Siswa Kosong Alpa',
        ]);
        Pelajar::create(['pelajar_id' => $kosong->id, 'markas_id' => $kelas->markas_id]);

        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $hadir->id,
            'datang' => '2026-09-14 08:05:00',
            'status' => AbsensiStatus::HADIR,
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $izin->id,
            'status' => AbsensiStatus::IZIN,
            'keterangan' => 'urut',
        ]);

        Livewire::actingAs($admin)
            ->test(AbsensiSlot::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('jadwal_id', (string) $slot->id)
            ->call('tandaiSisaAlpa')
            ->assertHasNoErrors()
            ->assertSee('Alpa');

        $this->assertSame(AbsensiStatus::HADIR, (int) AbsensiPelajar::query()->where('pelajar_id', $hadir->id)->value('status'));
        $this->assertSame(AbsensiStatus::IZIN, (int) AbsensiPelajar::query()->where('pelajar_id', $izin->id)->value('status'));
        $row = AbsensiPelajar::query()->where('pelajar_id', $kosong->id)->firstOrFail();
        $this->assertSame(AbsensiStatus::ALPA, (int) $row->status);
        $this->assertNull($row->datang);
        $this->assertSame(0, AbsensiPendidik::count());
    }

    private function slotFixture(): array
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $guru->update(['nomor_registrasi' => 'GURU01']);
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
            'nomor_registrasi' => 'ABC123',
        ]);
        Pelajar::create(['pelajar_id' => $siswa->id, 'markas_id' => $kelas->markas_id]);

        return [$admin, $kelas, $mapel, $guru, $slot, $siswa];
    }

    private function ownMarkasFixture(): array
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $guru->id, 'markas_id' => $genteng->id]);

        return [$admin, $kelas, $mapel, $guru];
    }
}
