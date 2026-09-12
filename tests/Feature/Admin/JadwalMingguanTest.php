<?php

namespace Tests\Feature\Admin;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Kelas;
use App\Livewire\Admin\JadwalMingguan;
use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\Support\AdminVisibility;
use App\User;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class JadwalMingguanTest extends TestCase
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

    public function test_admin_kelas_for_jadwal_is_limited_to_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $a = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        Kelas::create(['nama' => 'Tanpa Markas', 'markas_id' => null]);

        $ids = AdminVisibility::kelasForJadwal($admin)->pluck('kelas.id')->all();
        $this->assertEquals([$a->id], $ids);
    }

    public function test_admin_jadwal_query_hides_other_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $ka = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $kb = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        $mine = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $ka->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kb->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        $ids = AdminVisibility::jadwalQuery($admin)->pluck('adm_jadwal.id')->all();
        $this->assertEquals([$mine->id], $ids);
    }

    public function test_non_admin_cannot_mount_jadwal_mingguan(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 4]))
            ->test(JadwalMingguan::class)
            ->assertForbidden();
    }

    public function test_list_shows_only_slots_in_selected_week_and_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $kelasLain = Kelas::create(['nama' => 'B', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $fisika = Mapel::create(['mapel' => 'Fisika']);
        $kimia = Mapel::create(['mapel' => 'Kimia']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Satu']);
        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $fisika->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:00:00',
        ]);
        Jadwal::create([
            'staf_id' => 1,
            'mapel_id' => $kimia->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelasLain->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($this->superAdmin())
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->assertSee('Matematika')
            ->assertSee('Guru Satu')
            ->assertSee('08:00')
            ->assertDontSeeHtml('<p class="fw-semibold mb-0">Fisika</p>')
            ->assertDontSeeHtml('<p class="fw-semibold mb-0">Kimia</p>');
    }

    public function test_empty_senin_falls_back_to_monday_heading(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $monday = now()->startOfWeek(Carbon::MONDAY);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '')
            ->assertSee('Senin')
            ->assertSee($monday->format('d M Y'))
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:00')
            ->set('jam_selesai', '09:00')
            ->call('simpan')
            ->assertHasNoErrors();

        $row = Jadwal::firstOrFail();
        $this->assertSame($monday->toDateString(), $row->mulai->toDateString());
        $this->assertSame(Carbon::MONDAY, $row->mulai->dayOfWeek);
    }

    public function test_admin_can_create_slot_in_own_markas(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:00')
            ->set('jam_selesai', '09:30')
            ->call('simpan')
            ->assertHasNoErrors();

        $row = Jadwal::firstOrFail();
        $this->assertSame($admin->id, (int) $row->staf_id);
        $this->assertSame('2026-09-14 08:00:00', $row->mulai->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-14 09:30:00', $row->selesai->format('Y-m-d H:i:s'));
    }

    public function test_overlapping_slot_is_rejected(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:30')
            ->set('jam_selesai', '09:30')
            ->call('simpan')
            ->assertHasErrors(['jam_mulai' => 'Jam bentrok dengan slot lain']);

        $this->assertSame(1, Jadwal::count());
    }

    public function test_adjacent_slots_are_allowed(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '09:00')
            ->set('jam_selesai', '10:00')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame(2, Jadwal::count());
    }

    public function test_admin_cannot_create_slot_for_other_markas_kelas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $guru->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '08:00')
            ->set('jam_selesai', '09:00')
            ->call('simpan')
            ->assertHasErrors(['kelas_id'])
            ->assertSee('The selected kelas id is invalid.');
    }

    public function test_admin_cannot_assign_pendidik_from_other_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guruGenteng = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Genteng']);
        $guruJember = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Jember']);
        Pendidik::create(['pendidik_id' => $guruGenteng->id, 'markas_id' => $genteng->id]);
        Pendidik::create(['pendidik_id' => $guruJember->id, 'markas_id' => $jember->id]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->assertSee('Guru Genteng')
            ->assertDontSee('Guru Jember')
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guruJember->id)
            ->set('jam_mulai', '08:00')
            ->set('jam_selesai', '09:00')
            ->call('simpan')
            ->assertHasErrors(['pendidik_id']);

        $this->assertSame(0, Jadwal::count());
    }

    public function test_can_update_slot_times(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('ubah', $row->id)
            ->set('jam_mulai', '10:00')
            ->set('jam_selesai', '11:00')
            ->call('simpan')
            ->assertHasNoErrors();

        $fresh = $row->fresh();
        $this->assertSame('10:00', $fresh->mulai->format('H:i'));
        $this->assertSame('2026-09-14 10:00:00', $fresh->mulai->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-14 11:00:00', $fresh->selesai->format('Y-m-d H:i:s'));
        $this->assertSame(1, Jadwal::count());
    }

    public function test_update_does_not_change_staf_id(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $creator = User::factory()->create(['role_id' => 2]);
        $row = Jadwal::create([
            'staf_id' => $creator->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('ubah', $row->id)
            ->set('jam_mulai', '10:00')
            ->set('jam_selesai', '11:00')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame($creator->id, (int) $row->fresh()->staf_id);
    }

    public function test_ubah_fills_form_and_shows_edit_ui(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->call('ubah', $row->id)
            ->assertSet('editId', $row->id)
            ->assertSet('hari', '0')
            ->assertSet('mapel_id', (string) $mapel->id)
            ->assertSet('pendidik_id', (string) $guru->id)
            ->assertSet('jam_mulai', '08:00')
            ->assertSet('jam_selesai', '09:00')
            ->assertSee('Ubah slot')
            ->assertSee('Simpan perubahan')
            ->assertSee('Batal')
            ->assertSeeHtml('wire:confirm="Hapus slot ini?"');
    }

    public function test_can_hapus_unused_slot(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('hapus', $row->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('adm_jadwal', ['id' => $row->id]);
    }

    public function test_hapus_blocked_when_absensi_exists(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => User::factory()->create(['role_id' => 4])->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('hapus', $row->id)
            ->assertHasErrors(['jadwal' => 'Jadwal sudah dipakai absensi']);

        $this->assertDatabaseHas('adm_jadwal', ['id' => $row->id]);
    }

    public function test_hapus_clears_stale_jadwal_error_banner(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $locked = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $locked->id,
            'pelajar_id' => User::factory()->create(['role_id' => 4])->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => 1,
        ]);
        $ok = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 10:00:00',
            'selesai' => '2026-09-14 11:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('hapus', $locked->id)
            ->assertHasErrors(['jadwal' => 'Jadwal sudah dipakai absensi'])
            ->call('hapus', $ok->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('adm_jadwal', ['id' => $ok->id]);
        $this->assertDatabaseHas('adm_jadwal', ['id' => $locked->id]);
    }

    public function test_hapus_blocked_when_absensi_pendidik_exists(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        AbsensiPendidik::create([
            'jadwal_id' => $row->id,
            'pendidik_id' => $guru->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('hapus', $row->id)
            ->assertHasErrors(['jadwal' => 'Jadwal sudah dipakai absensi']);

        $this->assertDatabaseHas('adm_jadwal', ['id' => $row->id]);
    }

    public function test_ubah_and_update_blocked_when_absensi_exists(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);
        AbsensiPelajar::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => User::factory()->create(['role_id' => 4])->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('ubah', $row->id)
            ->assertHasErrors(['jadwal' => 'Jadwal sudah dipakai absensi'])
            ->assertSet('editId', null);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '10:00')
            ->set('jam_selesai', '11:00')
            ->set('editId', $row->id)
            ->call('simpan')
            ->assertHasErrors(['jadwal' => 'Jadwal sudah dipakai absensi']);

        $this->assertSame('2026-09-14 08:00:00', $row->fresh()->mulai->format('Y-m-d H:i:s'));
    }

    public function test_admin_cannot_hapus_other_markas_slot(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);
        $kelas = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3]);
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->call('hapus', $row->id)
            ->assertForbidden();

        $this->assertDatabaseHas('adm_jadwal', ['id' => $row->id]);
    }

    public function test_tampered_edit_id_cannot_edit_other_markas_slot(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $jember = Markas::create(['markas' => 'Jember']);
        $kelasLain = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelasLain->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        Livewire::actingAs($admin)
            ->test(JadwalMingguan::class)
            ->set('kelas_id', (string) $kelas->id)
            ->set('senin', '2026-09-14')
            ->set('hari', '0')
            ->set('mapel_id', (string) $mapel->id)
            ->set('pendidik_id', (string) $guru->id)
            ->set('jam_mulai', '10:00')
            ->set('jam_selesai', '11:00')
            ->set('editId', $row->id)
            ->call('simpan')
            ->assertForbidden();

        $this->assertSame('2026-09-14 08:00:00', $row->fresh()->mulai->format('Y-m-d H:i:s'));
        $this->assertSame(1, Jadwal::count());
    }

    public function test_sudah_ada_absensi_detects_pelajar_and_pendidik(): void
    {
        [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
        $row = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-14 08:00:00',
            'selesai' => '2026-09-14 09:00:00',
        ]);

        $this->assertFalse($row->sudahAdaAbsensi());

        AbsensiPelajar::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => User::factory()->create(['role_id' => 4])->id,
            'datang' => '2026-09-14 08:00:00',
            'status' => 1,
        ]);

        $this->assertTrue($row->fresh()->sudahAdaAbsensi());

        $lain = Jadwal::create([
            'staf_id' => $admin->id,
            'mapel_id' => $mapel->id,
            'pendidik_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mulai' => '2026-09-15 08:00:00',
            'selesai' => '2026-09-15 09:00:00',
        ]);
        AbsensiPendidik::create([
            'jadwal_id' => $lain->id,
            'pendidik_id' => $guru->id,
            'datang' => '2026-09-15 08:00:00',
            'status' => 1,
        ]);

        $this->assertTrue($lain->fresh()->sudahAdaAbsensi());
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

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }
}
