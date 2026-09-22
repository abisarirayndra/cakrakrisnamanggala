<?php

namespace Tests\Feature\Pelajar;

use App\BankOpsi;
use App\BankPaket;
use App\BankSoal;
use App\CatJadwal;
use App\CatJawaban;
use App\CatSesi;
use App\Livewire\Pelajar\CatTes;
use App\Mapel;
use App\Pendidik;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use App\User;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class CatTesTest extends TestCase
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

    public function test_non_pelajar_cannot_open_cat_tes(): void
    {
        $jadwal = $this->jadwalSiap();

        Livewire::actingAs(User::factory()->create(['role_id' => 3]))
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertForbidden();
    }

    public function test_valid_token_opens_tes_within_window(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => 'abcxyz'])
            ->assertRedirect(route('pelajar.cat.tes', $jadwal));

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertOk()
            ->assertSee('UTS Matematika')
            ->assertSee('Hasil dari 1+1')
            ->assertSee('2');
    }

    public function test_wrong_token_stays_on_form(): void
    {
        $this->jadwalSiap();

        $this->actingAs($this->pelajar())
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => 'SALAH1'])
            ->assertRedirect(route('pelajar.masukkan_token'));
    }

    public function test_token_rejected_before_window(): void
    {
        Carbon::setTestNow('2026-09-21 07:00:00');
        $jadwal = $this->jadwalSiap();

        $this->actingAs($this->pelajar())
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.masukkan_token'));

        $this->assertSame(0, CatSesi::count());
    }

    public function test_token_rejected_after_window(): void
    {
        Carbon::setTestNow('2026-09-21 10:00:00');
        $jadwal = $this->jadwalSiap();

        $this->actingAs($this->pelajar())
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.masukkan_token'));

        $this->assertSame(0, CatSesi::count());
    }

    public function test_pelajar_can_answer_tunggal_and_submit_score(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $soal = BankSoal::firstOrFail();

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertSee('ck-nav-nomor', false)
            ->call('pilihJawaban', 'B')
            ->assertSee('ck-nav-nomor-isi', false)
            ->call('kumpulkan')
            ->assertSee('Tes selesai')
            ->assertSee('10');

        $sesi = CatSesi::firstOrFail();
        $this->assertSame(CatSesi::SELESAI, $sesi->status);
        $this->assertSame(10, (int) $sesi->nilai);
        $this->assertSame('B', CatJawaban::where('sesi_id', $sesi->id)->where('bank_soal_id', $soal->id)->value('kode'));
    }

    public function test_pembobotan_uses_option_points(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalPembobotan();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'C')
            ->call('kumpulkan')
            ->assertSee('Tes selesai')
            ->assertSee('4');

        $this->assertSame(4, (int) CatSesi::firstOrFail()->nilai);
    }

    public function test_hasil_menampilkan_benar_salah_dan_skor(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();
        $paket = BankPaket::firstOrFail();

        $soalSalah = BankSoal::create([
            'paket_id' => $paket->id,
            'pendidik_id' => $paket->pendidik_id,
            'mapel_id' => $paket->mapel_id,
            'soal' => 'Hasil dari 2+2',
            'poin' => 10,
            'kunci' => 'B',
        ]);
        $this->opsi($soalSalah, ['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6']);

        $soalKosong = BankSoal::create([
            'paket_id' => $paket->id,
            'pendidik_id' => $paket->pendidik_id,
            'mapel_id' => $paket->mapel_id,
            'soal' => 'Hasil dari 3+3',
            'poin' => 10,
            'kunci' => 'C',
        ]);
        $this->opsi($soalKosong, ['A' => '5', 'B' => '7', 'C' => '6', 'D' => '9']);

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'B')
            ->call('keSoal', 1)
            ->call('pilihJawaban', 'A')
            ->call('kumpulkan')
            ->assertSee('Tes selesai')
            ->assertSeeInOrder(['Benar', '1', 'Salah', '2', 'Total skor', '10'])
            ->assertSee('Unduh PDF')
            ->assertSee(route('pelajar.cat.tes.pdf', $jadwal, false), false);
    }

    public function test_pelajar_can_download_report_pdf_after_submit(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan');

        $response = $this->actingAs($pelajar)
            ->get(route('pelajar.cat.tes.pdf', $jadwal));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('laporan-cat-uts-matematika.pdf', $response->headers->get('content-disposition'));
    }

    public function test_cannot_download_report_pdf_before_submit(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->get(route('pelajar.cat.tes.pdf', $jadwal))
            ->assertForbidden();
    }

    public function test_capaian_empty_before_submit(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->actingAs($pelajar)
            ->get(route('pelajar.capaian'))
            ->assertOk()
            ->assertSee('Belum ada tes yang dikumpulkan')
            ->assertDontSee('UTS Matematika')
            ->assertDontSee('id="grafik-capaian"', false);
    }

    public function test_capaian_shows_finished_tes_score_and_chart(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan');

        $this->actingAs($pelajar)
            ->get(route('pelajar.capaian'))
            ->assertOk()
            ->assertSee('Histori Tes')
            ->assertSee('UTS Matematika')
            ->assertSee('Skor')
            ->assertSee('10')
            ->assertDontSee('Bank UTS')
            ->assertDontSee('Bank soal')
            ->assertSee('grafik-capaian', false)
            ->assertSee(route('pelajar.cat.tes.pdf', $jadwal, false), false)
            ->assertDontSee('Belum ada tes yang dikumpulkan');
    }

    public function test_capaian_hides_other_pelajar_tes(): void
    {
        $pelajar = $this->pelajar();
        $lain = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Lain']);
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan');

        $this->actingAs($lain)
            ->get(route('pelajar.capaian'))
            ->assertOk()
            ->assertSee('Belum ada tes yang dikumpulkan')
            ->assertDontSee('UTS Matematika');
    }

    public function test_cannot_change_answers_after_submit(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        $page = Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan')
            ->call('pilihJawaban', 'A');

        $this->assertSame('B', CatJawaban::value('kode'));
        $this->assertSame(10, (int) CatSesi::value('nilai'));
        $page->assertSee('Tes selesai');
    }

    public function test_token_blocked_after_submit(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('kumpulkan');

        $this->actingAs($pelajar)
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.masukkan_token'));
    }

    public function test_time_up_auto_collects_and_blocks_token(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->call('pilihJawaban', 'B');

        Carbon::setTestNow('2026-09-21 09:30:00');

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertSee('Tes selesai');

        $this->assertSame(CatSesi::SELESAI, CatSesi::value('status'));
        $this->assertSame(10, (int) CatSesi::value('nilai'));

        $this->actingAs($pelajar)
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.masukkan_token'));
    }

    public function test_countdown_uses_red_accent_under_five_minutes(): void
    {
        Carbon::setTestNow('2026-09-21 09:26:00');
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertSee('ck-sisa-waktu', false)
            ->assertSee('ck-sisa-waktu-genting', false)
            ->assertSee('ck-sisa-garis-isi', false)
            ->assertSee('width: 4.44%', false);
    }

    public function test_many_banks_share_token_and_time(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalSiap();
        $guru = User::query()->where('nama', 'Guru CAT')->firstOrFail();
        $paketSatu = $jadwal->banks->first();
        $paketDua = $this->paket($guru, BankSoalTipe::TUNGGAL, 'Bank Kedua');
        $soalDua = BankSoal::create([
            'paket_id' => $paketDua->id,
            'pendidik_id' => $guru->id,
            'mapel_id' => $paketDua->mapel_id,
            'soal' => 'Hasil dari 2+2',
            'poin' => 10,
            'kunci' => 'B',
        ]);
        $this->opsi($soalDua, ['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6']);
        $jadwal->pasangBanks([$paketSatu->id, $paketDua->id]);

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.cat.tes', $jadwal));

        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        $page = Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertSee('Pilih bank soal')
            ->assertSee('Bank UTS')
            ->assertSee('Bank Kedua')
            ->assertDontSee('skor')
            ->assertDontSee('Hasil dari 1+1')
            ->assertDontSee('Hasil dari 2+2')
            ->call('pilihBank', $paketSatu->id)
            ->assertSee('Hasil dari 1+1')
            ->assertDontSee('Hasil dari 2+2')
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan')
            ->assertSee('Tes selesai')
            ->assertSeeInOrder(['Bank UTS', 'Selesai', '10', 'Bank Kedua', 'Belum', '-', 'Total', '10']);

        $this->actingAs($pelajar)
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.cat.tes', $jadwal));

        $page->call('kePilihan')
            ->call('pilihBank', $paketDua->id)
            ->assertSee('Hasil dari 2+2')
            ->assertDontSee('Hasil dari 1+1')
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan')
            ->assertSeeInOrder(['Bank UTS', '10', 'Bank Kedua', '10', 'Total', '20']);

        $this->assertSame(2, CatSesi::count());
        $this->assertSame(20, (int) CatSesi::sum('nilai'));

        $this->actingAs($pelajar)
            ->from(route('pelajar.masukkan_token'))
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token])
            ->assertRedirect(route('pelajar.masukkan_token'));
    }

    public function test_starting_sesi_stores_shuffled_soal_ids_per_bank(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalBanyakSoal(['Pertama', 'Kedua', 'Ketiga', 'Keempat']);

        $this->actingAs($pelajar)
            ->post(route('pelajar.submit_token'), ['token' => $jadwal->token]);
        $this->withSession(['cat_akses_jadwal' => $jadwal->id]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id]);

        $ids = BankSoal::query()->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $urutan = array_map('intval', CatSesi::firstOrFail()->urutan_soal ?? []);

        $this->assertEqualsCanonicalizing($ids, $urutan);
        $this->assertCount(count($ids), array_unique($urutan));
    }

    public function test_pelajar_sees_soal_in_stored_sesi_order_and_keeps_it(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalBanyakSoal(['Pertama', 'Kedua', 'Ketiga']);
        $ids = BankSoal::query()->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();

        CatSesi::create([
            'jadwal_id' => $jadwal->id,
            'pelajar_id' => $pelajar->id,
            'bank_paket_id' => BankPaket::firstOrFail()->id,
            'status' => CatSesi::BERJALAN,
            'urutan_soal' => [$ids[2], $ids[0], $ids[1]],
            'started_at' => now(),
        ]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertSee('Ketiga')
            ->assertDontSee('Pertama')
            ->call('keSoal', 1)
            ->assertSee('Pertama')
            ->assertDontSee('Ketiga')
            ->call('keSoal', 0)
            ->assertSee('Ketiga');

        $this->assertSame(
            [$ids[2], $ids[0], $ids[1]],
            array_map('intval', CatSesi::firstOrFail()->urutan_soal)
        );
    }

    public function test_shuffled_soal_still_scores_by_soal_id(): void
    {
        $pelajar = $this->pelajar();
        $jadwal = $this->jadwalBanyakSoal(['Pertama', 'Kedua']);
        $ids = BankSoal::query()->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
        BankSoal::query()->whereKey($ids[1])->update(['kunci' => 'C', 'poin' => 7]);

        CatSesi::create([
            'jadwal_id' => $jadwal->id,
            'pelajar_id' => $pelajar->id,
            'bank_paket_id' => BankPaket::firstOrFail()->id,
            'status' => CatSesi::BERJALAN,
            'urutan_soal' => [$ids[1], $ids[0]],
            'started_at' => now(),
        ]);

        Livewire::actingAs($pelajar)
            ->test(CatTes::class, ['jadwal' => $jadwal->id])
            ->assertSee('Kedua')
            ->call('pilihJawaban', 'C')
            ->call('keSoal', 1)
            ->call('pilihJawaban', 'B')
            ->call('kumpulkan')
            ->assertSee('Tes selesai')
            ->assertSee('7');

        $this->assertSame(7, (int) CatSesi::firstOrFail()->nilai);
    }

    private function pelajar(): User
    {
        return User::factory()->create([
            'role_id' => 4,
            'nama' => 'Siswa CAT',
        ]);
    }

    private function jadwalSiap(): CatJadwal
    {
        $guru = $this->guru();
        $paket = $this->paket($guru, BankSoalTipe::TUNGGAL, 'Bank UTS');
        $soal = BankSoal::create([
            'paket_id' => $paket->id,
            'pendidik_id' => $guru->id,
            'mapel_id' => $paket->mapel_id,
            'soal' => 'Hasil dari 1+1',
            'poin' => 10,
            'kunci' => 'B',
        ]);
        $this->opsi($soal, ['A' => '1', 'B' => '2', 'C' => '3', 'D' => '4']);

        $row = CatJadwal::create([
            'admin_id' => User::factory()->create(['role_id' => 2])->id,
            'nama' => 'UTS Matematika',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => 'ABCXYZ',
        ]);
        $row->pasangBanks([$paket->id]);

        return $row;
    }

    private function jadwalPembobotan(): CatJadwal
    {
        $guru = $this->guru();
        $paket = $this->paket($guru, BankSoalTipe::PEMBOBOTAN, 'Bank TKP');
        $soal = BankSoal::create([
            'paket_id' => $paket->id,
            'pendidik_id' => $guru->id,
            'mapel_id' => $paket->mapel_id,
            'soal' => 'Sikap terbaik',
            'poin' => null,
            'kunci' => null,
        ]);
        $this->opsi($soal, ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'], ['A' => 1, 'B' => 2, 'C' => 4, 'D' => 5]);

        $row = CatJadwal::create([
            'admin_id' => User::factory()->create(['role_id' => 2])->id,
            'nama' => 'Tes TKP',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => 'TKP001',
        ]);
        $row->pasangBanks([$paket->id]);

        return $row;
    }

    private function jadwalBanyakSoal(array $daftar): CatJadwal
    {
        $guru = $this->guru();
        $paket = $this->paket($guru, BankSoalTipe::TUNGGAL, 'Bank Acak');

        foreach ($daftar as $teks) {
            $soal = BankSoal::create([
                'paket_id' => $paket->id,
                'pendidik_id' => $guru->id,
                'mapel_id' => $paket->mapel_id,
                'soal' => $teks,
                'poin' => 5,
                'kunci' => 'A',
            ]);
            $this->opsi($soal, ['A' => '1', 'B' => '2', 'C' => '3', 'D' => '4']);
        }

        $row = CatJadwal::create([
            'admin_id' => User::factory()->create(['role_id' => 2])->id,
            'nama' => 'Tes Acak',
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => 'ACAK01',
        ]);
        $row->pasangBanks([$paket->id]);

        return $row;
    }

    private function guru(): User
    {
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru CAT']);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'markas_id' => 1,
        ]);

        return $guru->fresh();
    }

    private function paket(User $guru, string $tipe, string $nama): BankPaket
    {
        return BankPaket::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $guru->pendidik?->mapel_id,
            'nama' => $nama,
            'tipe' => $tipe,
            'bentuk' => BankSoalBentuk::BIASA,
        ]);
    }

    private function opsi(BankSoal $soal, array $teks, array $poin = []): void
    {
        $urutan = 1;
        foreach ($teks as $kode => $isi) {
            BankOpsi::create([
                'bank_soal_id' => $soal->id,
                'kode' => $kode,
                'teks' => $isi,
                'poin' => $poin[$kode] ?? 0,
                'urutan' => $urutan++,
            ]);
        }
    }
}
