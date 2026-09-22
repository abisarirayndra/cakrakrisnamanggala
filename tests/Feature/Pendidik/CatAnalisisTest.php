<?php

namespace Tests\Feature\Pendidik;

use App\BankOpsi;
use App\BankPaket;
use App\BankSoal;
use App\CatAnalisisCatatan;
use App\CatJadwal;
use App\CatJawaban;
use App\CatSesi;
use App\Kelas;
use App\Livewire\Pendidik\CatAnalisis;
use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class CatAnalisisTest extends TestCase
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
        $this->setUpArsipSchema();
    }

    public function test_non_pendidik_cannot_open_cat_analisis(): void
    {
        $guru = $this->guru();
        $jadwal = $this->jadwalDenganBank($guru, 'Bank Analisis');

        Livewire::actingAs(User::factory()->create(['role_id' => 2]))
            ->test(CatAnalisis::class, ['jadwal' => $jadwal->id])
            ->assertForbidden();
    }

    public function test_analisis_hub_lists_cat_paket_with_own_bank(): void
    {
        $guru = $this->guru();
        $lain = $this->guru('Guru Lain', 'PKN');
        $this->jadwalDenganBank($guru, 'Bank Saya', 'UTS Analisis');
        $this->jadwalDenganBank($lain, 'Bank Orang', 'UTS Orang', 'ORANG1');

        $this->actingAs($guru)
            ->get(route('pendidik.dinas.analisis'))
            ->assertOk()
            ->assertSee('Paket CAT')
            ->assertSee('UTS Analisis')
            ->assertSee('Bank Saya')
            ->assertSee(route('pendidik.cat.analisis', CatJadwal::where('nama', 'UTS Analisis')->first(), false), false)
            ->assertDontSee('UTS Orang')
            ->assertDontSee('Bank Orang');
    }

    public function test_analisis_detail_shows_per_soal_benar_salah(): void
    {
        $guru = $this->guru();
        $paket = $this->paketDenganSoal($guru, 'Bank Analisis', [
            ['soal' => 'Ibu kota Indonesia', 'kunci' => 'A', 'poin' => 5],
            ['soal' => 'Lambang negara', 'kunci' => 'B', 'poin' => 5],
        ]);
        $jadwal = $this->buatJadwal($guru, $paket, 'UTS Analisis');
        $kelas = Kelas::create(['nama' => 'Kelas A']);
        $pelajar = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Analisis', 'kelas_id' => $kelas->id]);
        $soal = $paket->soal()->orderBy('id')->get();

        $sesi = CatSesi::create([
            'jadwal_id' => $jadwal->id,
            'pelajar_id' => $pelajar->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 5,
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now()->subMinutes(5),
        ]);
        CatJawaban::create([
            'sesi_id' => $sesi->id,
            'bank_soal_id' => $soal[0]->id,
            'kode' => 'A',
            'poin' => 5,
        ]);
        CatJawaban::create([
            'sesi_id' => $sesi->id,
            'bank_soal_id' => $soal[1]->id,
            'kode' => 'A',
            'poin' => 0,
        ]);

        Livewire::actingAs($guru)
            ->test(CatAnalisis::class, ['jadwal' => $jadwal->id])
            ->assertOk()
            ->assertSee('UTS Analisis')
            ->assertSee('Analisis soal')
            ->assertSee('Hasil siswa')
            ->assertSee('Unduh analisis')
            ->assertSee(route('pendidik.cat.analisis.soal.pdf', $jadwal, false), false)
            ->assertSee('Ibu kota Indonesia')
            ->assertSee('Lambang negara')
            ->assertSeeInOrder(['Benar', 'Salah'])
            ->assertSee('100%')
            ->assertSee('0%')
            ->assertDontSee('Siswa Analisis')
            ->call('pilihTab', 'siswa')
            ->assertSee('Siswa Analisis')
            ->assertSee('Unduh analisis')
            ->assertSee(route('pendidik.cat.analisis.pdf', $jadwal, false), false)
            ->assertSeeInOrder(['Rank', 'Nama', 'Kelas', 'Status', 'Benar', 'Salah', 'Kosong', 'Skor'])
            ->assertSee('Detail')
            ->assertDontSee('Catatan analisis')
            ->call('bukaDetail', $pelajar->id)
            ->assertSee('Catatan analisis')
            ->assertSee('Ibu kota Indonesia')
            ->assertSee('Lambang negara')
            ->assertSee('ck-modal-backdrop', false)
            ->set('catatan.'.$pelajar->id, 'Perlu latihan soal lambang negara.')
            ->call('simpanCatatan', $pelajar->id)
            ->assertSee('Tersimpan');

        $this->assertSame(
            'Perlu latihan soal lambang negara.',
            CatAnalisisCatatan::query()
                ->where('jadwal_id', $jadwal->id)
                ->where('pelajar_id', $pelajar->id)
                ->where('pendidik_id', $guru->id)
                ->value('catatan')
        );
    }

    public function test_pendidik_can_download_analisis_siswa_pdf(): void
    {
        $guru = $this->guru();
        $paket = $this->paketDenganSoal($guru, 'Bank Pdf');
        $jadwal = $this->buatJadwal($guru, $paket, 'UTS Pdf', 'ANAPDF');
        $kelas = Kelas::create(['nama' => 'Kelas A']);
        $pelajar = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Pdf', 'kelas_id' => $kelas->id]);
        CatSesi::create([
            'jadwal_id' => $jadwal->id,
            'pelajar_id' => $pelajar->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 5,
            'started_at' => now()->subMinutes(10),
            'submitted_at' => now(),
        ]);
        CatAnalisisCatatan::create([
            'jadwal_id' => $jadwal->id,
            'pelajar_id' => $pelajar->id,
            'pendidik_id' => $guru->id,
            'catatan' => 'Catatan uji pdf.',
        ]);

        $response = $this->actingAs($guru)
            ->get(route('pendidik.cat.analisis.pdf', $jadwal));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('analisis-siswa-uts-pdf.pdf', $response->headers->get('content-disposition'));
    }

    public function test_pendidik_can_download_analisis_soal_pdf(): void
    {
        $guru = $this->guru();
        $paket = $this->paketDenganSoal($guru, 'Bank Soal Pdf', [
            ['soal' => 'Ibu kota Indonesia', 'kunci' => 'A', 'poin' => 5],
        ]);
        $jadwal = $this->buatJadwal($guru, $paket, 'UTS Soal Pdf', 'SOALPDF');
        $kelas = Kelas::create(['nama' => 'Kelas A']);
        $pelajar = User::factory()->create(['role_id' => 4, 'nama' => 'Siswa Soal Pdf', 'kelas_id' => $kelas->id]);
        CatSesi::create([
            'jadwal_id' => $jadwal->id,
            'pelajar_id' => $pelajar->id,
            'bank_paket_id' => $paket->id,
            'status' => CatSesi::SELESAI,
            'nilai' => 5,
            'started_at' => now()->subMinutes(10),
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($guru)
            ->get(route('pendidik.cat.analisis.soal.pdf', $jadwal));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('analisis-soal-uts-soal-pdf.pdf', $response->headers->get('content-disposition'));
    }

    public function test_analisis_hides_other_pendidik_bank_soal(): void
    {
        $guru = $this->guru();
        $lain = $this->guru('Guru Lain', 'PKN');
        $bankSaya = $this->paketDenganSoal($guru, 'Bank Saya', [
            ['soal' => 'Soal milik saya', 'kunci' => 'A', 'poin' => 5],
        ]);
        $bankLain = $this->paketDenganSoal($lain, 'Bank Orang', [
            ['soal' => 'Soal milik orang', 'kunci' => 'A', 'poin' => 5],
        ]);
        $jadwal = $this->buatJadwal($guru, [$bankSaya, $bankLain], 'UTS Campur');

        Livewire::actingAs($guru)
            ->test(CatAnalisis::class, ['jadwal' => $jadwal->id])
            ->assertOk()
            ->assertSee('Soal milik saya')
            ->assertDontSee('Soal milik orang');
    }

    public function test_pendidik_cannot_open_analisis_without_own_bank(): void
    {
        $guru = $this->guru();
        $lain = $this->guru('Guru Lain', 'PKN');
        $jadwal = $this->jadwalDenganBank($lain, 'Bank Orang', 'UTS Orang', 'ORANG1');

        Livewire::actingAs($guru)
            ->test(CatAnalisis::class, ['jadwal' => $jadwal->id])
            ->assertForbidden();

        $this->actingAs($guru)
            ->get(route('pendidik.cat.analisis.pdf', $jadwal))
            ->assertForbidden();

        $this->actingAs($guru)
            ->get(route('pendidik.cat.analisis.soal.pdf', $jadwal))
            ->assertForbidden();
    }

    private function setUpArsipSchema(): void
    {
        Schema::create('dn_pakets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_paket')->nullable();
            $table->timestamps();
        });

        Schema::table('dn_tes', function (Blueprint $table) {
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->unsignedInteger('pengajar_id')->nullable();
        });

        Schema::create('dn_arsippaket', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode')->nullable();
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    private function guru(string $nama = 'Guru Analisis', string $mapel = 'Matematika'): User
    {
        $markas = Markas::query()->first() ?: Markas::create(['markas' => 'Genteng']);
        $row = Mapel::query()->where('mapel', $mapel)->first() ?: Mapel::create(['mapel' => $mapel]);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => $nama]);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $row->id,
            'markas_id' => $markas->id,
        ]);

        return $guru->fresh();
    }

    private function jadwalDenganBank(User $guru, string $bank, string $nama = 'UTS Analisis', string $token = 'ANA001'): CatJadwal
    {
        return $this->buatJadwal($guru, $this->paketDenganSoal($guru, $bank), $nama, $token);
    }

    private function paketDenganSoal(User $guru, string $nama, array $soal = []): BankPaket
    {
        $paket = BankPaket::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $guru->pendidik?->mapel_id,
            'nama' => $nama,
            'tipe' => BankSoalTipe::TUNGGAL,
            'bentuk' => BankSoalBentuk::BIASA,
        ]);

        $daftar = $soal !== [] ? $soal : [['soal' => 'Soal uji', 'kunci' => 'A', 'poin' => 5]];
        foreach ($daftar as $item) {
            $row = BankSoal::create([
                'paket_id' => $paket->id,
                'pendidik_id' => $guru->id,
                'mapel_id' => $paket->mapel_id,
                'soal' => $item['soal'],
                'poin' => $item['poin'],
                'kunci' => $item['kunci'],
            ]);
            foreach (['A' => 'Satu', 'B' => 'Dua', 'C' => 'Tiga', 'D' => 'Empat'] as $kode => $teks) {
                BankOpsi::create([
                    'bank_soal_id' => $row->id,
                    'kode' => $kode,
                    'teks' => $teks,
                    'poin' => 0,
                    'urutan' => ord($kode) - 64,
                ]);
            }
        }

        return $paket->fresh('soal');
    }

    private function buatJadwal(User $guru, BankPaket|array $paket, string $nama, string $token = 'ANA001'): CatJadwal
    {
        $row = CatJadwal::create([
            'admin_id' => User::factory()->create(['role_id' => 2])->id,
            'nama' => $nama,
            'mulai' => '2026-09-21 08:00:00',
            'selesai' => '2026-09-21 09:30:00',
            'token' => $token,
        ]);
        $list = is_array($paket) ? $paket : [$paket];
        $row->pasangBanks(collect($list)->map->id->all());

        return $row;
    }
}
