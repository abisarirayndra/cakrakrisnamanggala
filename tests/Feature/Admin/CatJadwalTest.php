<?php

namespace Tests\Feature\Admin;

use App\BankPaket;
use App\BankSoal;
use App\CatJadwal;
use App\CatJawaban;
use App\CatSesi;
use App\Kelas;
use App\Livewire\Admin\CatJadwal as CatJadwalPage;
use App\Livewire\Admin\CatJadwalReport;
use App\Livewire\Admin\CatJadwalSkor;
use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use App\User;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class CatJadwalTest extends TestCase
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
    }

    public function test_non_admin_cannot_open_cat_jadwal(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 3]))
            ->test(CatJadwalPage::class)
            ->assertForbidden();
    }

    public function test_sidebar_folds_cat_with_jadwal(): void
    {
        $admin = $this->markasAdmin();

        $html = $this->actingAs($admin)
            ->get(route('admin.cat.jadwal'))
            ->assertOk()
            ->assertSee('Jadwal CAT')
            ->assertSee('js-nav-cat', false)
            ->assertSee(route('admin.cat.jadwal', absolute: false), false)
            ->assertDontSee(route('admin.dinas.paket', absolute: false), false)
            ->assertDontSee('data-nav="cat-paket"', false)
            ->getContent();

        $this->assertMatchesRegularExpression('/class="[^"]*js-nav-cat[^"]*show/', $html);
        $this->assertStringContainsString('data-nav="cat-jadwal"', $html);
    }

    public function test_pendidik_filter_limits_bank_options(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);

        $guruA = $this->guruAt($markasId, $mapel->id, 'Guru Alpha');
        $guruB = $this->guruAt($markasId, $mapel->id, 'Guru Beta');
        $paketA = $this->paketDenganSoal($guruA, 'Bank Alpha');
        $this->paketDenganSoal($guruB, 'Bank Beta');

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->assertSee('Filter pendidik')
            ->assertSee('js-select2-pendidik', false)
            ->assertSee('Cari pendidik')
            ->assertSee('Guru Alpha')
            ->assertSee('Guru Beta')
            ->assertDontSee('Bank Alpha')
            ->assertDontSee('Bank Beta')
            ->set('pendidik_id', (string) $guruA->id)
            ->assertSee('Bank Alpha')
            ->assertDontSee('Bank Beta')
            ->set('pendidik_id', (string) $guruB->id)
            ->assertSee('Bank Beta')
            ->assertDontSee('Bank Alpha')
            ->assertSet('bank_paket_id', '');
    }

    public function test_markas_admin_cannot_see_other_markas_pendidik_bank(): void
    {
        $admin = $this->markasAdmin();
        $lain = Markas::create(['markas' => 'Banyuwangi']);
        $mapel = Mapel::create(['mapel' => 'PKN']);
        $guruLain = $this->guruAt($lain->id, $mapel->id, 'Guru Luar');
        $this->paketDenganSoal($guruLain, 'Bank Luar');

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->assertDontSee('Guru Luar')
            ->assertDontSee('Bank Luar');
    }

    public function test_admin_creates_many_banks_with_shared_time_and_token(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = $this->guruAt($markasId, $mapel->id, 'Guru Jadwal');
        $paketA = $this->paketDenganSoal($guru, 'Paket UTS');
        $paketB = $this->paketDenganSoal($guru, 'Paket TKP');

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->assertDontSee('Kelas')
            ->assertSee('Tambah bank')
            ->set('nama', 'UTS Matematika')
            ->set('mulai', '2026-09-20T08:00')
            ->set('selesai', '2026-09-20T09:30')
            ->set('pendidik_id', (string) $guru->id)
            ->set('bank_paket_id', (string) $paketA->id)
            ->call('tambahBank')
            ->assertSee('Paket UTS')
            ->set('bank_paket_id', (string) $paketB->id)
            ->call('tambahBank')
            ->assertSee('Paket TKP')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Paket UTS')
            ->assertSee('Paket TKP')
            ->assertSee('Token tes');

        $row = CatJadwal::firstOrFail();
        $this->assertSame('UTS Matematika', $row->nama);
        $this->assertSame($admin->id, (int) $row->admin_id);
        $this->assertEqualsCanonicalizing([$paketA->id, $paketB->id], $row->banks()->pluck('cat_bank_paket.id')->all());
        $this->assertSame(6, strlen((string) $row->token));
        $this->assertSame('2026-09-20 08:00:00', $row->mulai->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-20 09:30:00', $row->selesai->format('Y-m-d H:i:s'));
    }

    public function test_admin_can_regenerate_tes_token(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = $this->guruAt($markasId, $mapel->id, 'Guru Token');
        $paket = $this->paketDenganSoal($guru, 'Bank Token');

        $row = $this->buatJadwal($admin, $paket, [
            'nama' => 'Sesi Token',
            'token' => 'AAAAAA',
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->call('perbaruiToken', $row->id)
            ->assertHasNoErrors();

        $baru = $row->fresh();
        $this->assertNotSame('AAAAAA', $baru->token);
        $this->assertSame(6, strlen((string) $baru->token));
    }

    public function test_cannot_pick_bank_outside_pendidik_filter(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guruA = $this->guruAt($markasId, $mapel->id, 'Guru A');
        $guruB = $this->guruAt($markasId, $mapel->id, 'Guru B');
        $paketB = $this->paketDenganSoal($guruB, 'Paket B');

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->set('nama', 'Sesi Salah')
            ->set('pendidik_id', (string) $guruA->id)
            ->set('bank_paket_id', (string) $paketB->id)
            ->call('tambahBank')
            ->assertHasErrors('bank_paket_id');

        $this->assertSame(0, CatJadwal::count());
    }

    public function test_admin_can_edit_paket_without_changing_token(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guruA = $this->guruAt($markasId, $mapel->id, 'Guru A');
        $guruB = $this->guruAt($markasId, $mapel->id, 'Guru B');
        $paketA = $this->paketDenganSoal($guruA, 'Bank A');
        $paketB = $this->paketDenganSoal($guruB, 'Bank B');

        $row = $this->buatJadwal($admin, $paketA, [
            'nama' => 'Sesi Lama',
            'token' => 'ABCDEF',
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->call('ubah', $row->id)
            ->assertSet('nama', 'Sesi Lama')
            ->assertSet('pendidik_id', (string) $guruA->id)
            ->assertSee('Ubah paket')
            ->assertSee('Bank A')
            ->call('hapusBank', 0)
            ->set('nama', 'Sesi Baru')
            ->set('pendidik_id', (string) $guruB->id)
            ->set('bank_paket_id', (string) $paketB->id)
            ->call('tambahBank')
            ->set('mulai', '2026-09-22T10:00')
            ->set('selesai', '2026-09-22T11:30')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Sesi Baru')
            ->assertSee('Bank B');

        $baru = $row->fresh();
        $this->assertSame('Sesi Baru', $baru->nama);
        $this->assertEquals([$paketB->id], $baru->banks()->pluck('cat_bank_paket.id')->all());
        $this->assertSame('ABCDEF', $baru->token);
        $this->assertSame('2026-09-22 10:00:00', $baru->mulai->format('Y-m-d H:i:s'));
        $this->assertSame('2026-09-22 11:30:00', $baru->selesai->format('Y-m-d H:i:s'));
    }

    public function test_admin_can_show_chat_text_for_pelajar(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = $this->guruAt($markasId, $mapel->id, 'Guru Chat');
        $paket = $this->paketDenganSoal($guru, 'Bank Chat');

        $row = $this->buatJadwal($admin, $paket, [
            'nama' => 'UTS Matematika',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => 'XYZ123',
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->assertSee('Teks chat')
            ->call('lihatChat', $row->id)
            ->assertSee('Teks chat pelajar')
            ->assertSee('Nama Paket: UTS Matematika')
            ->assertSee('Waktu Pelaksanaan:')
            ->assertSee('Total Waktu: 1 jam 30 menit')
            ->assertSee('Token: XYZ123')
            ->assertSee('Salin');

        $this->assertStringContainsString('Nama Paket: UTS Matematika', $row->teksChat());
        $this->assertStringContainsString('Total Waktu: 1 jam 30 menit', $row->teksChat());
        $this->assertStringContainsString('Token: XYZ123', $row->teksChat());
    }

    public function test_admin_sees_live_skor_per_paket(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = $this->guruAt($markasId, $mapel->id, 'Guru Skor');
        $paket = $this->paketDenganSoal($guru, 'Bank Skor');
        $soal = BankSoal::firstOrFail();

        $paketDua = $this->paketDenganSoal($guru, 'Bank Dua');
        $row = $this->buatJadwal($admin, [$paket, $paketDua], [
            'nama' => 'UTS Live',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => 'LIVE01',
        ]);

        $kelasA = Kelas::create(['nama' => 'Kelas A', 'markas_id' => $markasId]);
        $kelasB = Kelas::create(['nama' => 'Kelas B', 'markas_id' => $markasId]);
        $alpha = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Alpha', 'kelas_id' => $kelasA->id]);
        $beta = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Beta', 'kelas_id' => $kelasB->id]);

        $sesiAlpha = CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $alpha->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::BERJALAN,
            'started_at' => now(),
        ]);
        CatJawaban::create([
            'sesi_id' => $sesiAlpha->id,
            'bank_soal_id' => $soal->id,
            'kode' => 'A',
            'poin' => 5,
        ]);

        CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $beta->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 0,
            'started_at' => now(),
            'submitted_at' => now(),
        ]);
        CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $beta->id,
            'bank_paket_id' => $paketDua->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 0,
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalPage::class)
            ->assertSee('Live skor')
            ->assertSee(route('admin.cat.jadwal.skor', $row, false), false)
            ->assertSee('Report')
            ->assertSee(route('admin.cat.jadwal.report', $row, false), false);

        Livewire::actingAs($admin)
            ->test(CatJadwalSkor::class, ['jadwal' => $row->id])
            ->assertOk()
            ->assertSee('Live skor')
            ->assertSee('UTS Live')
            ->assertSee('wire:poll', false)
            ->assertSeeInOrder(['Rank', 'Nama', 'Kelas', 'Status', 'Bank Skor', 'Bank Dua', 'Total'])
            ->assertDontSee('>Benar<', false)
            ->assertDontSee('>Salah<', false)
            ->assertSeeInOrder(['1', 'Siswa Alpha', 'Kelas A', 'Berjalan', '5', '-', '5', '2', 'Siswa Beta', 'Kelas B', 'Selesai', '0', '0', '0']);
    }

    public function test_admin_sees_report_per_paket(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = $this->guruAt($markasId, $mapel->id, 'Guru Report');
        $paket = $this->paketDenganSoal($guru, 'Bank Report');
        $soal = BankSoal::firstOrFail();
        $paketDua = $this->paketDenganSoal($guru, 'Bank Dua Report');

        $row = $this->buatJadwal($admin, [$paket, $paketDua], [
            'nama' => 'UTS Report',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => 'REP001',
        ]);

        $kelasA = Kelas::create(['nama' => 'Kelas A', 'markas_id' => $markasId]);
        $kelasB = Kelas::create(['nama' => 'Kelas B', 'markas_id' => $markasId]);
        $alpha = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Alpha', 'kelas_id' => $kelasA->id]);
        $beta = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Beta', 'kelas_id' => $kelasB->id]);

        $sesiAlpha = CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $alpha->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::BERJALAN,
            'started_at' => now(),
        ]);
        CatJawaban::create([
            'sesi_id' => $sesiAlpha->id,
            'bank_soal_id' => $soal->id,
            'kode' => 'A',
            'poin' => 5,
        ]);

        CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $beta->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 0,
            'started_at' => now(),
            'submitted_at' => now(),
        ]);
        CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $beta->id,
            'bank_paket_id' => $paketDua->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 0,
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalReport::class, ['jadwal' => $row->id])
            ->assertOk()
            ->assertSee('Report paket')
            ->assertSee('UTS Report')
            ->assertSeeInOrder(['Rank', 'Nama', 'Kelas', 'Status', 'Bank Report', 'Bank Dua Report', 'Total'])
            ->assertDontSee('>Benar<', false)
            ->assertDontSee('>Salah<', false)
            ->assertSeeInOrder(['1', 'Siswa Alpha', 'Kelas A', 'Berjalan', '5', '-', '5', '2', 'Siswa Beta', 'Kelas B', 'Selesai', '0', '0', '0'])
            ->assertSee(route('admin.cat.jadwal.report.pdf', $row, false), false);
    }

    public function test_admin_can_download_report_pdf_per_paket(): void
    {
        $admin = $this->markasAdmin();
        $markasId = $admin->markasIds()[0];
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = $this->guruAt($markasId, $mapel->id, 'Guru Pdf');
        $paket = $this->paketDenganSoal($guru, 'Bank Pdf');

        $row = $this->buatJadwal($admin, $paket, [
            'nama' => 'UTS Pdf',
            'token' => 'PDF001',
        ]);

        $kelas = Kelas::create(['nama' => 'Kelas A', 'markas_id' => $markasId]);
        $pelajar = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Pdf', 'kelas_id' => $kelas->id]);
        CatSesi::create([
            'jadwal_id' => $row->id,
            'pelajar_id' => $pelajar->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 5,
            'started_at' => now(),
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.cat.jadwal.report.pdf', $row));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('laporan-paket-uts-pdf.pdf', $response->headers->get('content-disposition'));
    }

    public function test_markas_admin_cannot_open_other_markas_live_skor(): void
    {
        $admin = $this->markasAdmin();
        $lain = Markas::create(['markas' => 'Banyuwangi']);
        $mapel = Mapel::create(['mapel' => 'PKN']);
        $guruLain = $this->guruAt($lain->id, $mapel->id, 'Guru Luar Skor');
        $paket = $this->paketDenganSoal($guruLain, 'Bank Luar Skor');

        $row = $this->buatJadwal(User::factory()->create(['role_id' => 2]), $paket, [
            'nama' => 'Sesi Luar',
            'token' => 'LUAR01',
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalSkor::class, ['jadwal' => $row->id])
            ->assertForbidden();
    }

    public function test_markas_admin_cannot_open_other_markas_report(): void
    {
        $admin = $this->markasAdmin();
        $lain = Markas::create(['markas' => 'Banyuwangi']);
        $mapel = Mapel::create(['mapel' => 'PKN']);
        $guruLain = $this->guruAt($lain->id, $mapel->id, 'Guru Luar Report');
        $paket = $this->paketDenganSoal($guruLain, 'Bank Luar Report');

        $row = $this->buatJadwal(User::factory()->create(['role_id' => 2]), $paket, [
            'nama' => 'Sesi Luar Report',
            'token' => 'LUAR02',
        ]);

        Livewire::actingAs($admin)
            ->test(CatJadwalReport::class, ['jadwal' => $row->id])
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.cat.jadwal.report.pdf', $row))
            ->assertForbidden();
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

    private function guruAt(int $markasId, int $mapelId, string $nama): User
    {
        $guru = User::factory()->create(['role_id' => 3, 'nama' => $nama]);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapelId,
            'markas_id' => $markasId,
        ]);

        return $guru->fresh();
    }

    private function paketDenganSoal(User $guru, string $nama): BankPaket
    {
        $paket = BankPaket::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $guru->pendidik?->mapel_id,
            'nama' => $nama,
            'tipe' => BankSoalTipe::TUNGGAL,
            'bentuk' => BankSoalBentuk::BIASA,
        ]);

        BankSoal::create([
            'paket_id' => $paket->id,
            'pendidik_id' => $guru->id,
            'mapel_id' => $paket->mapel_id,
            'soal' => 'Soal uji',
            'poin' => 5,
            'kunci' => 'A',
        ]);

        return $paket;
    }

    private function buatJadwal(User $admin, BankPaket|array $paket, array $data = []): CatJadwal
    {
        $row = CatJadwal::create(array_merge([
            'admin_id' => $admin->id,
            'nama' => 'Sesi',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:00:00',
            'token' => 'ABCDEF',
        ], $data));

        $list = is_array($paket) ? $paket : [$paket];
        $row->pasangBanks(collect($list)->map->id->all());

        return $row;
    }
}
