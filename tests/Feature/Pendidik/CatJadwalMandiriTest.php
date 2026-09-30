<?php

namespace Tests\Feature\Pendidik;

use App\BankPaket;
use App\BankSoal;
use App\CatJadwal;
use App\Livewire\Pendidik\CatJadwal as CatJadwalPage;
use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\Support\AdminVisibility;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class CatJadwalMandiriTest extends TestCase
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

    public function test_non_pendidik_cannot_open_jadwal_mandiri(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 2]))
            ->test(CatJadwalPage::class)
            ->assertForbidden();
    }

    public function test_sidebar_shows_jadwal_cat_under_cat_menu(): void
    {
        $guru = $this->guru('Guru Menu');

        $html = $this->actingAs($guru)
            ->get(route('pendidik.cat.jadwal'))
            ->assertOk()
            ->assertSee('Jadwal CAT')
            ->assertSee('Tambah Jadwal')
            ->assertSee('js-nav-cat', false)
            ->assertSee(route('pendidik.cat.jadwal', absolute: false), false)
            ->getContent();

        $this->assertMatchesRegularExpression('/class="[^"]*js-nav-cat[^"]*show/', $html);
        $this->assertStringContainsString('data-nav="cat-jadwal"', $html);
    }

    public function test_pendidik_creates_jadwal_from_own_banks_only(): void
    {
        $guru = $this->guru('Guru Mandiri');
        $lain = $this->guru('Guru Lain');
        $milik = $this->paket($guru, 'Bank Milik');
        $kedua = $this->paket($guru, 'Bank Kedua');
        $this->paket($lain, 'Bank Orang Lain');

        Livewire::actingAs($guru)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->assertSee('Bank Milik')
            ->assertSee('Bank Kedua')
            ->assertDontSee('Bank Orang Lain')
            ->assertDontSee('Filter pendidik')
            ->set('nama', 'Latihan Mandiri')
            ->set('mulai', '2026-09-29T08:00')
            ->set('selesai', '2026-09-29T09:00')
            ->set('bank_paket_id', (string) $milik->id)
            ->call('tambahBank')
            ->assertSee('Bank Milik')
            ->set('bank_paket_id', (string) $kedua->id)
            ->call('tambahBank')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Latihan Mandiri')
            ->assertSee('Token tes');

        $row = CatJadwal::firstOrFail();
        $this->assertSame($guru->id, (int) $row->admin_id);
        $this->assertEqualsCanonicalizing([$milik->id, $kedua->id], $row->banks()->pluck('cat_bank_paket.id')->all());
        $this->assertSame(6, strlen((string) $row->token));
    }

    public function test_pendidik_cannot_attach_bank_owned_by_someone_else(): void
    {
        $guru = $this->guru('Guru A');
        $lain = $this->guru('Guru B');
        $asing = $this->paket($lain, 'Bank Asing');

        Livewire::actingAs($guru)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->set('nama', 'Sesi Salah')
            ->set('mulai', '2026-09-29T08:00')
            ->set('selesai', '2026-09-29T09:00')
            ->set('bank_paket_id', (string) $asing->id)
            ->call('tambahBank')
            ->assertHasErrors('bank_paket_id');

        $this->assertSame(0, CatJadwal::count());
    }

    public function test_pendidik_cannot_save_tampered_foreign_bank(): void
    {
        $guru = $this->guru('Guru A');
        $lain = $this->guru('Guru B');
        $asing = $this->paket($lain, 'Bank Asing');

        Livewire::actingAs($guru)
            ->test(CatJadwalPage::class)
            ->call('bukaForm')
            ->set('nama', 'Sesi Curang')
            ->set('mulai', '2026-09-29T08:00')
            ->set('selesai', '2026-09-29T09:00')
            ->set('bankTerpilih', [[
                'id' => $asing->id,
                'nama' => 'Bank Asing',
                'mapel' => null,
                'tipe' => 'Tunggal',
                'soal_count' => 1,
            ]])
            ->call('simpan')
            ->assertHasErrors('bankTerpilih');

        $this->assertSame(0, CatJadwal::count());
    }

    public function test_pendidik_edits_own_jadwal_without_changing_token(): void
    {
        $guru = $this->guru('Guru Edit');
        $awal = $this->paket($guru, 'Bank Awal');
        $baru = $this->paket($guru, 'Bank Baru');
        $row = $this->jadwal($guru, $awal, ['nama' => 'Sesi Lama', 'token' => 'ABCDEF']);

        Livewire::actingAs($guru)
            ->test(CatJadwalPage::class)
            ->call('ubah', $row->id)
            ->assertSet('nama', 'Sesi Lama')
            ->assertSee('Ubah jadwal')
            ->call('hapusBank', 0)
            ->set('nama', 'Sesi Baru')
            ->set('bank_paket_id', (string) $baru->id)
            ->call('tambahBank')
            ->set('mulai', '2026-09-30T10:00')
            ->set('selesai', '2026-09-30T11:00')
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Sesi Baru');

        $fresh = $row->fresh();
        $this->assertSame('Sesi Baru', $fresh->nama);
        $this->assertSame('ABCDEF', $fresh->token);
        $this->assertEquals([$baru->id], $fresh->banks()->pluck('cat_bank_paket.id')->all());
    }

    public function test_pendidik_cannot_change_jadwal_created_by_someone_else(): void
    {
        $guru = $this->guru('Guru Sendiri');
        $admin = User::factory()->create(['role_id' => 2]);
        $paket = $this->paket($guru, 'Bank Resmi');
        $resmi = $this->jadwal($admin, $paket, ['nama' => 'Jadwal Admin', 'token' => 'ADMIN1']);

        $halaman = Livewire::actingAs($guru)
            ->test(CatJadwalPage::class)
            ->assertDontSee('Jadwal Admin');

        $this->expectException(ModelNotFoundException::class);
        $halaman->call('ubah', $resmi->id);

        $this->assertSame('Jadwal Admin', $resmi->fresh()->nama);
    }

    public function test_admin_markas_still_sees_jadwal_mandiri(): void
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $guru = $this->guru('Guru Terlihat', $markas->id);
        $paket = $this->paket($guru, 'Bank Terlihat');
        $row = $this->jadwal($guru, $paket, ['nama' => 'Mandiri Terlihat']);

        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($markas->id);

        $this->assertTrue(
            AdminVisibility::catJadwalQuery($admin)->whereKey($row->id)->exists()
        );
    }

    private function guru(string $nama, ?int $markasId = null): User
    {
        $markasId ??= Markas::create(['markas' => 'Markas '.$nama])->id;
        $mapel = Mapel::firstOrCreate(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => $nama]);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'markas_id' => $markasId,
        ]);

        return $guru->fresh();
    }

    private function paket(User $guru, string $nama): BankPaket
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

    private function jadwal(User $pembuat, BankPaket $paket, array $data = []): CatJadwal
    {
        $row = CatJadwal::create(array_merge([
            'admin_id' => $pembuat->id,
            'nama' => 'Sesi',
            'mulai' => '2026-09-29 08:00:00',
            'selesai' => '2026-09-29 09:00:00',
            'token' => 'TOKEN1',
        ], $data));
        $row->pasangBanks([$paket->id]);

        return $row;
    }
}
