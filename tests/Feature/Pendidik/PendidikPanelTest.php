<?php

namespace Tests\Feature\Pendidik;

use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class PendidikPanelTest extends TestCase
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
        $this->setUpPendidikHubSchema();
    }

    public function test_beranda_uses_cakra_shell_with_pendidik_nav(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.beranda'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSeeInOrder([
                'data-nav="beranda"',
                'data-nav="paket"',
                'data-nav="analisis"',
                'data-nav="absensi"',
            ], false)
            ->assertDontSee('data-nav="pelajar"', false)
            ->assertDontSee('data-nav="admin"', false)
            ->assertSee(route('pendidik.dinas.beranda', absolute: false), false)
            ->assertSee(route('pendidik.dinas.paket', absolute: false), false)
            ->assertSee(route('pendidik.dinas.analisis', absolute: false), false)
            ->assertSee(route('pendidik.absensi', absolute: false), false)
            ->assertSee('Keluar');
    }

    public function test_beranda_shows_profile_and_keeps_menu_until_biodata_complete(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.beranda'))
            ->assertOk()
            ->assertSee('Data Diri')
            ->assertSee('Guru Uji')
            ->assertSee('3510123456780001')
            ->assertSee('Matematika')
            ->assertSee('Paket Soal')
            ->assertSee('Absensi')
            ->assertDontSee('SILAKAN MELAKUKAN EDIT DATA DIRI');
    }

    public function test_incomplete_biodata_locks_beranda_menu(): void
    {
        $this->actingAs($this->pendidik(['tempat_lahir' => null]))
            ->get(route('pendidik.dinas.beranda'))
            ->assertOk()
            ->assertSee('SILAKAN MELAKUKAN EDIT DATA DIRI')
            ->assertSee('Not Available');
    }

    public function test_beranda_opens_attendance_card_from_modal_not_inline(): void
    {
        $html = $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.beranda'))
            ->assertOk()
            ->assertSee('Kartu Absensi')
            ->assertSee('data-bs-target="#kartu-absensi-modal"', false)
            ->assertSee('Unduh PDF')
            ->assertSee(route('pendidik.kartu-absensi', absolute: false), false)
            ->assertSee('GURU-001')
            ->assertSee('Matematika')
            ->assertDontSee('Cetak kartu')
            ->getContent();

        $dataDiriPos = strpos($html, 'Data Diri');
        $buttonPos = strpos($html, 'data-bs-target="#kartu-absensi-modal"');
        $menuPos = strpos($html, '>Menu</h2>');

        $this->assertNotFalse($dataDiriPos);
        $this->assertNotFalse($buttonPos);
        $this->assertNotFalse($menuPos);
        $this->assertGreaterThan($dataDiriPos, $buttonPos);
        $this->assertLessThan($menuPos, $buttonPos);
    }

    public function test_pendidik_can_download_attendance_card_pdf(): void
    {
        $response = $this->actingAs($this->pendidik())
            ->get(route('pendidik.kartu-absensi'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('kartu-absensi-GURU-001.pdf', $response->headers->get('content-disposition'));
    }

    public function test_absensi_hub_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.absensi'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Histori Mengajar')
            ->assertSee('Kelas')
            ->assertSee('Bulan')
            ->assertSee('Tahun')
            ->assertDontSee('Kode QR')
            ->assertDontSee('Absensi Pendidik');
    }

    public function test_histori_mengajar_uses_cakra_shell(): void
    {
        $user = $this->pendidik();

        $this->actingAs($user)
            ->get(route('pendidik.absensi.histori-mengajar', [
                'kelas' => 1,
                'bulan' => '09',
                'tahun' => '2026',
            ]))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Histori Mengajar')
            ->assertSee('Kelas')
            ->assertSee('Bulan')
            ->assertSee('Tahun');
    }

    public function test_paket_list_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.paket'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Paket Soal')
            ->assertSee('Daftar Paket Soal');
    }

    public function test_tes_list_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.tes', 1))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Daftar Tes');
    }

    public function test_analisis_hub_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.analisis'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Analisis Nilai')
            ->assertSee('Daftar Arsip Nilai');
    }

    public function test_penilaian_hub_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.penilaian', 1))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Hasil Penilaian');
    }

    public function test_edit_profil_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik())
            ->get(route('pendidik.dinas.edit'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Formulir Profil Pendidik')
            ->assertSee(route('pendidik.dinas.updateprofil', absolute: false), false);
    }

    public function test_jasmani_hub_uses_cakra_shell(): void
    {
        $this->actingAs($this->pendidik(['mapel' => 'Jasmani']))
            ->get(route('pendidik.absensi.jadwal_jasmani'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Jadwal Hari Ini')
            ->assertSee('data-nav="jasmani"', false);
    }

    private function pendidik(array $overrides = []): User
    {
        $mapelName = $overrides['mapel'] ?? 'Matematika';
        unset($overrides['mapel']);

        $tempatLahir = array_key_exists('tempat_lahir', $overrides)
            ? $overrides['tempat_lahir']
            : 'Banyuwangi';
        unset($overrides['tempat_lahir']);

        $markas = Markas::query()->first() ?: Markas::create(['markas' => 'Genteng']);
        $mapel = Mapel::query()->where('mapel', $mapelName)->first()
            ?: Mapel::create(['mapel' => $mapelName]);

        $user = User::factory()->create(array_merge([
            'role_id' => 3,
            'nama' => 'Guru Uji',
            'nomor_registrasi' => 'GURU-001',
        ], $overrides));

        Pendidik::create([
            'pendidik_id' => $user->id,
            'mapel_id' => $mapel->id,
            'markas_id' => $markas->id,
            'nik' => '3510123456780001',
            'nip' => '198012345',
            'tempat_lahir' => $tempatLahir,
            'tanggal_lahir' => '1990-01-15',
            'alamat' => 'Jl. Cakra 1',
            'wa' => '081234567890',
            'ibu' => 'Siti',
            'status_dapodik' => 'Aktif',
        ]);

        return $user;
    }

    private function setUpPendidikHubSchema(): void
    {
        Schema::table('adm_pendidik', function (Blueprint $table) {
            $table->string('cv')->nullable();
            $table->string('status_dapodik')->nullable();
            $table->string('markas')->nullable();
        });

        Schema::create('dn_pakets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_paket')->nullable();
            $table->unsignedInteger('kelas')->nullable();
            $table->integer('status')->nullable();
            $table->integer('kategori')->nullable();
            $table->timestamps();
        });

        Schema::table('dn_tes', function (Blueprint $table) {
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->float('nilai_pokok')->nullable();
            $table->dateTime('mulai')->nullable();
            $table->dateTime('selesai')->nullable();
            $table->unsignedInteger('pengajar_id')->nullable();
            $table->string('token')->nullable();
        });

        Schema::create('dn_arsippaket', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode')->nullable();
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });

        Schema::create('dn_penilaians', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dn_tes_id')->nullable();
            $table->unsignedInteger('pelajar_id')->nullable();
            $table->float('nilai')->nullable();
            $table->float('akumulasi')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }
}
