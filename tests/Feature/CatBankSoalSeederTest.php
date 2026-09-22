<?php

namespace Tests\Feature;

use App\BankPaket;
use App\CatJadwal;
use App\CatSesi;
use App\Mapel;
use App\Pendidik;
use App\User;
use Database\Seeders\CatBankSoalSeeder;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class CatBankSoalSeederTest extends TestCase
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

    public function test_seeder_fills_random_questions_and_open_jadwal(): void
    {
        $mapel = Mapel::create(['mapel' => 'PKN']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Seed']);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapel->id,
        ]);
        User::factory()->create(['role_id' => 2, 'nama' => 'Admin Seed']);

        $this->seed(CatBankSoalSeeder::class);

        $this->assertSame(3, BankPaket::count());
        $this->assertGreaterThanOrEqual(20, BankPaket::where('nama', '[SEED] Jawaban Tunggal')->firstOrFail()->soal()->count());
        $this->assertSame(10, BankPaket::where('nama', '[SEED] Matematis')->firstOrFail()->soal()->count());
        $this->assertSame(15, BankPaket::where('nama', '[SEED] Pembobotan')->firstOrFail()->soal()->count());

        $jadwal = CatJadwal::where('token', CatBankSoalSeeder::TOKEN)->firstOrFail();
        $this->assertSame('[SEED] UTS Uji Coba', $jadwal->nama);
        $this->assertSame(3, $jadwal->banks()->count());
        $this->assertGreaterThan(0, $jadwal->daftarSoal()->count());
        $this->assertTrue($jadwal->banks->every(fn ($bank) => $bank->jadwal->contains($jadwal)));

        $alpha = User::query()->where('email', CatBankSoalSeeder::EMAIL_ALPHA)->firstOrFail();
        $beta = User::query()->where('email', CatBankSoalSeeder::EMAIL_BETA)->firstOrFail();
        $this->assertSame('Siswa Seed Alpha', $alpha->nama);
        $this->assertSame('[SEED] Kelas A', $alpha->kelas?->nama);
        $this->assertSame('[SEED] Kelas B', $beta->kelas?->nama);

        $this->assertSame(CatSesi::BERJALAN, CatSesi::query()->where('pelajar_id', $alpha->id)->value('status'));
        $this->assertSame(CatSesi::SELESAI, CatSesi::query()->where('pelajar_id', $beta->id)->value('status'));
        $this->assertSame(1, CatJadwal::count());

        $selesaiA = User::query()->where('email', CatBankSoalSeeder::EMAIL_SELESAI_A)->firstOrFail();
        $selesaiB = User::query()->where('email', CatBankSoalSeeder::EMAIL_SELESAI_B)->firstOrFail();
        $this->assertSame('Siswa Seed Selesai A', $selesaiA->nama);
        $this->assertSame('Siswa Seed Selesai B', $selesaiB->nama);
        $this->assertTrue($jadwal->fresh()->semuaSelesaiUntuk($selesaiA->id));
        $this->assertTrue($jadwal->fresh()->semuaSelesaiUntuk($selesaiB->id));
        $this->assertSame(3, CatSesi::query()->where('pelajar_id', $selesaiA->id)->where('status', CatSesi::SELESAI)->count());
        $this->assertSame(3, CatSesi::query()->where('pelajar_id', $selesaiB->id)->where('status', CatSesi::SELESAI)->count());
        $this->assertGreaterThan(
            (int) CatSesi::query()->where('pelajar_id', $selesaiB->id)->sum('nilai'),
            (int) CatSesi::query()->where('pelajar_id', $selesaiA->id)->sum('nilai')
        );
    }
}
