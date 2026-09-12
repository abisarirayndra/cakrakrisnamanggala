<?php

namespace Tests\Feature\Pendaftaran;

use App\Livewire\Pendaftaran\WizardPendaftaran;
use App\Markas;
use App\Pelajar;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\Concerns\CreatesPendaftaranSchema;
use Tests\TestCase;

class WizardPendaftaranTest extends TestCase
{
    use CreatesPendaftaranSchema;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('filesystems.disks.pelajar_foto', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/disks/pelajar_foto'),
            'url' => '/img/pelajar',
            'visibility' => 'public',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPendaftaranSchema();
        Storage::fake('pelajar_foto');
    }

    public function test_register_email_page_renders_the_livewire_wizard(): void
    {
        $this->get(route('register-email'))
            ->assertOk()
            ->assertSeeLivewire(WizardPendaftaran::class)
            ->assertDontSee('published Livewire assets are out of date', false);
    }

    public function test_step_one_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);

        Livewire::test(WizardPendaftaran::class)
            ->set('nama', 'Budi Santoso')
            ->set('email', 'ada@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('simpanAkun')
            ->assertHasErrors(['email']);
    }

    public function test_step_one_creates_pendaftar_account_and_advances(): void
    {
        Livewire::test(WizardPendaftaran::class)
            ->set('nama', 'Budi Santoso')
            ->set('email', 'budi@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('simpanAkun')
            ->assertHasNoErrors()
            ->assertSet('step', 2);

        $user = User::where('email', 'budi@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(5, (int) $user->role_id);
        $this->assertTrue(Hash::check('secret123', $user->password));
        $this->assertTrue(Pelajar::where('pelajar_id', $user->id)->exists());
        $this->assertAuthenticatedAs($user);
    }

    public function test_step_two_persists_existing_student_fields_and_shows_receipt(): void
    {
        $markas = $this->makeMarkas('Banyuwangi');
        $foto = UploadedFile::fake()->image('foto.jpg', 400, 300)->size(200);

        Livewire::test(WizardPendaftaran::class)
            ->set('nama', 'Siti Aminah')
            ->set('email', 'siti@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('simpanAkun')
            ->set('tempat_lahir', 'Jember')
            ->set('tanggal_lahir', '2007-04-12')
            ->set('nik', '3510010101070001')
            ->set('nisn', '0012345678')
            ->set('ibu', 'Sri Wahyuni')
            ->set('alamat', 'Jl. Melati 10')
            ->set('sekolah', 'SMAN 1 Genteng')
            ->set('status_sekolah', 0)
            ->set('wa', '081234567890')
            ->set('wali', 'Ahmad Fauzi')
            ->set('wa_wali', '081298765432')
            ->set('markas_id', $markas->id)
            ->set('foto', $foto)
            ->call('simpanBiodata')
            ->assertHasNoErrors()
            ->assertSet('step', 3)
            ->assertSee('Bukti Pendaftaran')
            ->assertSee('data-qr-url');

        $user = User::where('email', 'siti@example.com')->firstOrFail();
        $pelajar = Pelajar::where('pelajar_id', $user->id)->firstOrFail();

        $this->assertSame('Jember', $pelajar->tempat_lahir);
        $this->assertSame('3510010101070001', $pelajar->nik);
        $this->assertSame('0012345678', $pelajar->nisn);
        $this->assertSame('Sri Wahyuni', $pelajar->ibu);
        $this->assertSame('Jl. Melati 10', $pelajar->alamat);
        $this->assertSame('SMAN 1 Genteng', $pelajar->sekolah);
        $this->assertSame(0, (int) $pelajar->status_sekolah);
        $this->assertSame('081234567890', $pelajar->wa);
        $this->assertSame('Ahmad Fauzi', $pelajar->wali);
        $this->assertSame('081298765432', $pelajar->wa_wali);
        $this->assertSame($markas->id, (int) $pelajar->markas_id);
        $this->assertNotNull($pelajar->foto);
        $this->assertNull($user->nomor_registrasi);
        Storage::disk('pelajar_foto')->assertExists($pelajar->foto);

        $stored = Storage::disk('pelajar_foto')->path($pelajar->foto);
        [$width, $height] = getimagesize($stored);
        $this->assertSame(600, $width);
        $this->assertSame(800, $height);
    }

    public function test_signed_receipt_url_is_public_and_unsigned_is_rejected(): void
    {
        $markas = $this->makeMarkas('Genteng');
        $user = User::factory()->create(['role_id' => 5, 'nama' => 'Rina']);
        $pelajar = Pelajar::create([
            'pelajar_id' => $user->id,
            'tempat_lahir' => 'Banyuwangi',
            'tanggal_lahir' => '2006-01-01',
            'alamat' => 'Jl. Kenanga',
            'sekolah' => 'SMAN 2',
            'status_sekolah' => 1,
            'wa' => '08111',
            'wali' => 'Bapak',
            'wa_wali' => '08222',
            'foto' => 'Pelajar1.jpg',
            'markas_id' => $markas->id,
            'nik' => '123',
            'nisn' => '456',
            'ibu' => 'Ibu',
        ]);

        $this->get(route('pendaftar.bukti', $pelajar->id))
            ->assertForbidden();

        $this->get(URL::signedRoute('pendaftar.bukti', $pelajar->id))
            ->assertOk()
            ->assertSee('Rina')
            ->assertSee('Genteng');
    }

    private function makeMarkas(string $nama): Markas
    {
        $markas = new Markas();
        $markas->markas = $nama;
        $markas->save();

        return $markas;
    }
}
