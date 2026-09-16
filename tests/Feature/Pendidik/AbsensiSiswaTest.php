<?php

namespace Tests\Feature\Pendidik;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Kelas;
use App\Livewire\Pendidik\AbsensiSiswa;
use App\Livewire\Pendidik\HistoriAbsensiSiswa;
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

class AbsensiSiswaTest extends TestCase
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

    public function test_non_pendidik_cannot_mount_absensi_siswa(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 2]))
            ->test(AbsensiSiswa::class)
            ->assertForbidden();
    }

    public function test_sidebar_has_absensi_siswa_and_histori_submenu(): void
    {
        [$guru] = $this->guruFixture();

        $html = $this->actingAs($guru)
            ->get(route('pendidik.absensi.siswa'))
            ->assertOk()
            ->assertSee('Absensi Siswa')
            ->assertSee('Histori Absensi')
            ->assertSee(route('pendidik.absensi.siswa', absolute: false), false)
            ->assertSee(route('pendidik.absensi.histori-siswa', absolute: false), false)
            ->assertSee('js-nav-absensi', false)
            ->assertSee('ck-nav-chevron', false)
            ->getContent();

        $this->assertMatchesRegularExpression('/class="[^"]*js-nav-absensi[^"]*show/', $html);
    }

    public function test_lists_only_own_slots_today(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot] = $this->slotFixture();
        $lain = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $lain->id, 'markas_id' => $kelas->markas_id]);
        Jadwal::create([
            'staf_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $lain->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-16 10:00:00',
            'selesai' => '2026-09-16 11:00:00',
        ]);
        Jadwal::create([
            'staf_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-17 08:00:00',
            'selesai' => '2026-09-17 09:00:00',
        ]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->assertSee('Matematika')
            ->assertSee('08:00')
            ->assertDontSee('10:00');
    }

    public function test_checklist_lists_pelajar_kelas_without_telat_ontime(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Checklist']);
        $lainKelas = Kelas::create(['nama' => 'B', 'markas_id' => $kelas->markas_id]);
        $orangLain = User::factory()->create(['role_id' => 4, 'kelas_id' => $lainKelas->id, 'nama' => 'Siswa Kelas Lain']);
        Pelajar::create(['pelajar_id' => $orangLain->id, 'markas_id' => $kelas->markas_id]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->assertSee('Siswa Checklist')
            ->assertDontSee('Siswa Kelas Lain')
            ->assertDontSee('Telat')
            ->assertDontSee('Ontime');
    }

    public function test_check_writes_schedule_times_not_clock_times(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->call('toggle', $siswa->id)
            ->assertHasNoErrors();

        $row = AbsensiPelajar::firstOrFail();
        $this->assertSame($siswa->id, (int) $row->pelajar_id);
        $this->assertSame($slot->id, (int) $row->jadwal_id);
        $this->assertTrue($row->datang->equalTo($slot->mulai));
        $this->assertTrue($row->pulang->equalTo($slot->selesai));
        $this->assertSame(AbsensiStatus::HADIR, (int) $row->status);
    }

    public function test_uncheck_removes_hadir_row(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->call('toggle', $siswa->id)
            ->assertHasNoErrors();

        $this->assertSame(0, AbsensiPelajar::count());
    }

    public function test_toggle_renders_name_row_as_checked(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Centang Nama']);

        $html = Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->call('toggle', $siswa->id)
            ->html();

        $this->assertNotFalse(strpos($html, 'Siswa Centang Nama'));
        $this->assertMatchesRegularExpression('/checked[^>]*>[\s\S]{0,200}Siswa Centang Nama/', $html);
        $this->assertStringContainsString('wire:click="toggle('.$siswa->id.')"', $html);
        $this->assertStringNotContainsString('wire:click.prevent="toggle', $html);
    }

    public function test_izin_sakit_form_is_on_page(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Form Izin']);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->assertSee('Izin / Sakit')
            ->assertSee('Siswa Form Izin')
            ->assertSeeHtml('id="izin_user_id"')
            ->assertSeeHtml('id="izin_status"')
            ->assertSeeHtml('id="izin_keterangan"');
    }

    public function test_izin_requires_keterangan(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::IZIN)
            ->set('izin_keterangan', '')
            ->call('simpanIzin')
            ->assertHasErrors(['izin_keterangan' => 'Keterangan wajib untuk izin']);
    }

    public function test_sakit_without_keterangan_ok(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::SAKIT)
            ->set('izin_keterangan', '')
            ->call('simpanIzin')
            ->assertHasNoErrors();

        $row = AbsensiPelajar::firstOrFail();
        $this->assertSame($siswa->id, (int) $row->pelajar_id);
        $this->assertSame(AbsensiStatus::SAKIT, (int) $row->status);
        $this->assertNull($row->datang);
        $this->assertNull($row->pulang);
    }

    public function test_izin_rejected_when_already_hadir(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->set('izin_user_id', (string) $siswa->id)
            ->set('izin_status', (string) AbsensiStatus::IZIN)
            ->set('izin_keterangan', 'keluarga')
            ->call('simpanIzin')
            ->assertHasErrors(['izin_user_id' => 'Sudah hadir']);
    }

    public function test_history_shows_izin_status(): void
    {
        Carbon::setTestNow('2026-09-16 12:00:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Izin Histori']);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'status' => AbsensiStatus::IZIN,
            'keterangan' => 'urut',
        ]);

        Livewire::actingAs($guru)
            ->test(HistoriAbsensiSiswa::class)
            ->set('bulan', '09')
            ->set('tahun', '2026')
            ->assertSee('Siswa Izin Histori')
            ->assertSee('Izin')
            ->assertDontSee('Telat')
            ->assertDontSee('Ontime');
    }

    public function test_cannot_toggle_other_teacher_slot(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $lain = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $lain->id, 'markas_id' => $kelas->markas_id]);
        $slotLain = Jadwal::create([
            'staf_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $lain->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-16 10:00:00',
            'selesai' => '2026-09-16 11:00:00',
        ]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slotLain->id)
            ->call('toggle', $siswa->id)
            ->assertForbidden();

        $this->assertSame(0, AbsensiPelajar::count());
    }

    public function test_history_lists_checked_students_without_telat_ontime(): void
    {
        Carbon::setTestNow('2026-09-16 12:00:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Histori Hadir']);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
        ]);

        Livewire::actingAs($guru)
            ->test(HistoriAbsensiSiswa::class)
            ->set('bulan', '09')
            ->set('tahun', '2026')
            ->assertSee('Siswa Histori Hadir')
            ->assertSee('Matematika')
            ->assertDontSee('Telat')
            ->assertDontSee('Ontime');
    }

    public function test_search_filters_pelajar_by_nama(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Andi Pratama']);
        $teman = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Budi Santoso',
        ]);
        Pelajar::create(['pelajar_id' => $teman->id, 'markas_id' => $kelas->markas_id]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->set('cari', 'Andi')
            ->assertSee('Andi Pratama')
            ->assertSeeHtml('wire:click="toggle('.$siswa->id.')"')
            ->assertDontSeeHtml('wire:click="toggle('.$teman->id.')"');
    }

    public function test_can_save_jurnal_for_own_slot(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot] = $this->slotFixture();

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->set('jurnal', 'Aljabar linier')
            ->call('simpanJurnal')
            ->assertHasNoErrors();

        $row = AbsensiPendidik::firstOrFail();
        $this->assertSame($guru->id, (int) $row->pendidik_id);
        $this->assertSame($slot->id, (int) $row->jadwal_id);
        $this->assertSame('Aljabar linier', $row->jurnal);
        $this->assertTrue($row->datang->equalTo($slot->mulai));
        $this->assertTrue($row->pulang->equalTo($slot->selesai));
    }

    public function test_jurnal_is_required(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot] = $this->slotFixture();

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->set('jurnal', '')
            ->call('simpanJurnal')
            ->assertHasErrors(['jurnal']);
    }

    public function test_history_shows_jurnal_and_filters_by_nama(): void
    {
        Carbon::setTestNow('2026-09-16 12:00:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Histori Hadir']);
        $teman = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Nama Tidak Dicari',
        ]);
        Pelajar::create(['pelajar_id' => $teman->id, 'markas_id' => $kelas->markas_id]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $siswa->id,
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $teman->id,
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
        ]);
        AbsensiPendidik::create([
            'jadwal_id' => $slot->id,
            'pendidik_id' => $guru->id,
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
            'jurnal' => 'Pecahan desimal',
        ]);

        Livewire::actingAs($guru)
            ->test(HistoriAbsensiSiswa::class)
            ->set('bulan', '09')
            ->set('tahun', '2026')
            ->assertSee('Pecahan desimal')
            ->set('cari', 'Histori Hadir')
            ->assertSee('Siswa Histori Hadir')
            ->assertDontSee('Nama Tidak Dicari');
    }

    public function test_alpa_question_is_at_the_bottom(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $siswa->update(['nama' => 'Siswa Belum Absen']);

        $html = Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->html();

        $this->assertNotFalse(strpos($html, 'Siswa yang tidak terabsen dan tidak ada izin, statusnya Alpa?'));
        $this->assertGreaterThan(
            strpos($html, 'Siswa Belum Absen'),
            strpos($html, 'Siswa yang tidak terabsen dan tidak ada izin, statusnya Alpa?')
        );
    }

    public function test_confirm_alpa_marks_unmarked_students_only(): void
    {
        Carbon::setTestNow('2026-09-16 08:30:00');
        [$guru, $kelas, $mapel, $slot, $siswa] = $this->slotFixture();
        $hadir = $siswa;
        $hadir->update(['nama' => 'Siswa Hadir Alpa']);
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
            'datang' => $slot->mulai,
            'pulang' => $slot->selesai,
            'status' => AbsensiStatus::HADIR,
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $slot->id,
            'pelajar_id' => $izin->id,
            'status' => AbsensiStatus::IZIN,
            'keterangan' => 'urut',
        ]);

        Livewire::actingAs($guru)
            ->test(AbsensiSiswa::class)
            ->set('jadwal_id', (string) $slot->id)
            ->call('tandaiSisaAlpa')
            ->assertHasNoErrors()
            ->assertSee('Alpa');

        $this->assertSame(AbsensiStatus::HADIR, (int) AbsensiPelajar::query()->where('pelajar_id', $hadir->id)->value('status'));
        $this->assertSame(AbsensiStatus::IZIN, (int) AbsensiPelajar::query()->where('pelajar_id', $izin->id)->value('status'));
        $row = AbsensiPelajar::query()->where('pelajar_id', $kosong->id)->firstOrFail();
        $this->assertSame(AbsensiStatus::ALPA, (int) $row->status);
        $this->assertNull($row->datang);
        $this->assertNull($row->pulang);
    }

    private function slotFixture(): array
    {
        [$guru, $kelas, $mapel] = $this->guruFixture();
        $slot = Jadwal::create([
            'staf_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-16 08:00:00',
            'selesai' => '2026-09-16 09:00:00',
        ]);
        $siswa = User::factory()->create([
            'role_id' => 4,
            'kelas_id' => $kelas->id,
            'nama' => 'Siswa A',
        ]);
        Pelajar::create(['pelajar_id' => $siswa->id, 'markas_id' => $kelas->markas_id]);

        return [$guru, $kelas, $mapel, $slot, $siswa];
    }

    private function guruFixture(): array
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $markas->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Mapel']);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'markas_id' => $markas->id,
            'tempat_lahir' => 'Banyuwangi',
            'nik' => '3510123456780001',
        ]);

        return [$guru, $kelas, $mapel];
    }
}
