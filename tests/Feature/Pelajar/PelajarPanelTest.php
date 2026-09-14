<?php

namespace Tests\Feature\Pelajar;

use App\Kelas;
use App\Pelajar;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class PelajarPanelTest extends TestCase
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
        $this->setUpPelajarCatSchema();
    }

    public function test_beranda_uses_cakra_shell_with_pelajar_nav(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.dinas.beranda'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSeeInOrder([
                'data-nav="beranda"',
                'data-nav="cat"',
                'data-nav="capaian"',
                'data-nav="absensi"',
            ], false)
            ->assertDontSee('data-nav="pendaftar"', false)
            ->assertDontSee('data-nav="admin"', false)
            ->assertSee(route('pelajar.dinas.beranda', absolute: false), false)
            ->assertSee(route('pelajar.masukkan_token', absolute: false), false)
            ->assertSee(route('pelajar.capaian', absolute: false), false)
            ->assertSee(route('pelajar.absensi', absolute: false), false)
            ->assertSee('Keluar');
    }

    public function test_beranda_shows_profile_and_attendance_stats(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.dinas.beranda'))
            ->assertOk()
            ->assertSee('Data Diri')
            ->assertSee('Siswa Uji')
            ->assertSee('3510010101070001')
            ->assertSee('Jumlah Ontime')
            ->assertSee('Jumlah Terlambat')
            ->assertSee('Jumlah Izin');
    }

    public function test_beranda_opens_attendance_card_from_modal_not_inline(): void
    {
        $kelas = Kelas::create(['nama' => 'Reguler A']);

        $html = $this->actingAs($this->pelajar(['kelas_id' => $kelas->id]))
            ->get(route('pelajar.dinas.beranda'))
            ->assertOk()
            ->assertSee('Kartu Absensi')
            ->assertSee('data-bs-target="#kartu-absensi-modal"', false)
            ->assertSee('Unduh PDF')
            ->assertSee(route('pelajar.kartu-absensi', absolute: false), false)
            ->assertSee('CKM-001')
            ->assertSee('Reguler A')
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

    public function test_pelajar_can_download_attendance_card_pdf(): void
    {
        $kelas = Kelas::create(['nama' => 'Reguler A']);

        $response = $this->actingAs($this->pelajar(['kelas_id' => $kelas->id]))
            ->get(route('pelajar.kartu-absensi'));

        $response->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('kartu-absensi-CKM-001.pdf', $response->headers->get('content-disposition'));
    }

    public function test_absensi_hub_uses_cakra_shell(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.absensi'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Absensi Pelajar')
            ->assertSee('Kode QR')
            ->assertSee('Histori Pembelajaran');
    }

    public function test_histori_pembelajaran_uses_cakra_shell(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.absensi.histori-pembelajaran', [
                'bulan' => '09',
                'tahun' => '2026',
            ]))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Histori')
            ->assertSee('Bulan')
            ->assertSee('Tahun');
    }

    public function test_token_cat_uses_cakra_shell(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.masukkan_token'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Masukkan Token')
            ->assertSee(route('pelajar.submit_token', absolute: false), false);
    }

    public function test_capaian_uses_cakra_shell_without_scores(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.capaian'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('SKD tertinggi')
            ->assertSee('Tes Akademik Tertinggi')
            ->assertSee('Psikotes Tertinggi')
            ->assertSee('Grafik Capaian Tes');
    }

    public function test_paket_list_uses_cakra_shell(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.dinas.paket'))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Paket Soal')
            ->assertSee('Daftar Paket Soal');
    }

    public function test_tes_list_uses_cakra_shell(): void
    {
        $this->actingAs($this->pelajar())
            ->get(route('pelajar.dinas.tes', 1))
            ->assertOk()
            ->assertSee('ck-sidebar', false)
            ->assertSee('Daftar Tes');
    }

    private function pelajar(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'role_id' => 4,
            'nama' => 'Siswa Uji',
            'nomor_registrasi' => 'CKM-001',
            'kelas_id' => 1,
        ], $overrides));

        Pelajar::create([
            'pelajar_id' => $user->id,
            'nik' => '3510010101070001',
            'nisn' => '0012345678',
            'tempat_lahir' => 'Jember',
            'tanggal_lahir' => '2007-01-01',
            'alamat' => 'Jl. Melati 10',
            'sekolah' => 'SMAN 1 Genteng',
            'wali' => 'Ahmad Fauzi',
        ]);

        return $user;
    }

    private function setUpPelajarCatSchema(): void
    {
        Schema::create('dn_pakets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_paket')->nullable();
            $table->unsignedInteger('kelas')->nullable();
            $table->integer('status')->nullable();
            $table->integer('kategori')->nullable();
            $table->timestamps();
        });

        Schema::create('dn_kelas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->unsignedInteger('kelas_id')->nullable();
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

        Schema::create('dn_rekapdinas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pelajar_id');
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->float('total_nilai')->nullable();
            $table->timestamps();
        });

        Schema::create('dn_rekap_tnipolri', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pelajar_id');
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->float('total_nilai')->nullable();
            $table->timestamps();
        });

        Schema::create('dn_rekap_psikotes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pelajar_id');
            $table->unsignedInteger('dn_paket_id')->nullable();
            $table->float('total_nilai')->nullable();
            $table->timestamps();
        });
    }
}
