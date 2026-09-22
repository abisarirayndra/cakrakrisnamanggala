<?php

namespace Tests\Feature\Pendidik;

use App\BankOpsi;
use App\BankPaket;
use App\BankSoal;
use App\Livewire\Pendidik\BankPaket as BankPaketPage;
use App\Livewire\Pendidik\BankSoalForm;
use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\Concerns\CreatesCatBankSchema;
use Tests\TestCase;

class BankSoalTest extends TestCase
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

    public function test_non_pendidik_cannot_mount_bank_paket(): void
    {
        Livewire::actingAs(User::factory()->create(['role_id' => 2]))
            ->test(BankPaketPage::class)
            ->assertForbidden();
    }

    public function test_sidebar_folds_cat_with_bank_soal(): void
    {
        $guru = $this->guruFixture();

        $html = $this->actingAs($guru)
            ->get(route('pendidik.cat.bank-soal'))
            ->assertOk()
            ->assertSee('Bank Soal')
            ->assertSee('Tambah Paket')
            ->assertSee('js-nav-cat', false)
            ->assertSee(route('pendidik.cat.bank-soal', absolute: false), false)
            ->assertDontSee('data-nav="paket"', false)
            ->getContent();

        $this->assertMatchesRegularExpression('/class="[^"]*js-nav-cat[^"]*show/', $html);
        $this->assertStringContainsString('data-nav="bank-soal"', $html);
    }

    public function test_pendidik_creates_paket_with_locked_tipe(): void
    {
        $guru = $this->guruFixture();

        Livewire::actingAs($guru)
            ->test(BankPaketPage::class)
            ->call('bukaForm')
            ->set('nama', 'Matematika Dasar')
            ->set('tipe', BankSoalTipe::TUNGGAL)
            ->set('bentuk', BankSoalBentuk::MATEMATIS)
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Matematika Dasar')
            ->assertSee('Jawaban Tunggal')
            ->assertSee('Matematis');

        $paket = BankPaket::firstOrFail();
        $this->assertSame($guru->id, (int) $paket->pendidik_id);
        $this->assertSame(BankSoalTipe::TUNGGAL, $paket->tipe);
        $this->assertSame(BankSoalBentuk::MATEMATIS, $paket->bentuk);
    }

    public function test_pendidik_can_edit_paket(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL, 'Paket Lama', BankSoalBentuk::BIASA);

        Livewire::actingAs($guru)
            ->test(BankPaketPage::class)
            ->call('ubah', $paket->id)
            ->assertSet('nama', 'Paket Lama')
            ->assertSet('tipe', BankSoalTipe::TUNGGAL)
            ->assertSet('bentuk', BankSoalBentuk::BIASA)
            ->assertSee('Ubah paket')
            ->set('nama', 'Paket Baru')
            ->set('tipe', BankSoalTipe::PEMBOBOTAN)
            ->set('bentuk', BankSoalBentuk::MATEMATIS)
            ->call('simpan')
            ->assertHasNoErrors()
            ->assertSee('Paket Baru');

        $baru = $paket->fresh();
        $this->assertSame('Paket Baru', $baru->nama);
        $this->assertSame(BankSoalTipe::PEMBOBOTAN, $baru->tipe);
        $this->assertSame(BankSoalBentuk::MATEMATIS, $baru->bentuk);
    }

    public function test_pendidik_can_store_tunggal_question_inside_own_paket(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->assertDontSee('Tipe penilaian')
            ->set('soal', 'Hasil dari $1+1$ adalah')
            ->set('poin', '10')
            ->set('kunci', 'B')
            ->set('opsi', [
                'A' => '1',
                'B' => '2',
                'C' => '3',
                'D' => '4',
                'E' => '',
            ])
            ->call('simpan')
            ->assertHasNoErrors();

        $row = BankSoal::firstOrFail();
        $this->assertSame($paket->id, (int) $row->paket_id);
        $this->assertSame($guru->id, (int) $row->pendidik_id);
        $this->assertSame('Hasil dari $1+1$ adalah', $row->soal);
        $this->assertSame(10, (int) $row->poin);
        $this->assertSame('B', $row->kunci);
        $this->assertSame(['A', 'B', 'C', 'D'], $row->opsi()->pluck('kode')->all());
        $this->assertSame(0, BankOpsi::query()->where('kode', 'E')->count());
    }

    public function test_pembobotan_paket_stores_score_per_option(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::PEMBOBOTAN, 'Sikap');

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->set('soal', 'Sikap yang paling tepat')
            ->set('opsi', [
                'A' => 'Menolong',
                'B' => 'Diam',
                'C' => 'Menyindir',
                'D' => 'Mengabaikan',
                'E' => 'Mengejek',
            ])
            ->set('poinOpsi', [
                'A' => '5',
                'B' => '3',
                'C' => '1',
                'D' => '0',
                'E' => '-1',
            ])
            ->call('simpan')
            ->assertHasNoErrors();

        $row = BankSoal::firstOrFail();
        $this->assertNull($row->kunci);
        $this->assertNull($row->poin);
        $this->assertSame(5, (int) $row->opsi()->where('kode', 'A')->value('poin'));
        $this->assertSame(-1, (int) $row->opsi()->where('kode', 'E')->value('poin'));
    }

    public function test_pendidik_can_attach_image_to_question(): void
    {
        Storage::fake('public');
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru);
        $file = UploadedFile::fake()->image('grafik.png', 40, 40);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->set('soal', 'Perhatikan grafik')
            ->set('poin', '5')
            ->set('kunci', 'A')
            ->set('opsi.A', 'Naik')
            ->set('opsi.B', 'Turun')
            ->set('opsi.C', 'Datar')
            ->set('opsi.D', 'Tidak tahu')
            ->set('gambar', $file)
            ->call('simpan')
            ->assertHasNoErrors();

        $row = BankSoal::firstOrFail();
        $this->assertNotNull($row->gambar);
        Storage::disk('public')->assertExists($row->gambar);
    }

    public function test_list_hides_other_pendidik_paket(): void
    {
        $guru = $this->guruFixture();
        $lain = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Lain']);
        Pendidik::create(['pendidik_id' => $lain->id]);
        $this->paketFixture($lain, BankSoalTipe::TUNGGAL, 'Paket Orang Lain');

        Livewire::actingAs($guru)
            ->test(BankPaketPage::class)
            ->assertDontSee('Paket Orang Lain')
            ->assertSee('Belum ada paket');
    }

    public function test_cannot_open_other_pendidik_paket(): void
    {
        $guru = $this->guruFixture();
        $lain = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $lain->id]);
        $paketLain = $this->paketFixture($lain, BankSoalTipe::TUNGGAL, 'Rahasia');

        $this->actingAs($guru)
            ->get(route('pendidik.cat.bank-soal.paket', $paketLain))
            ->assertNotFound();
    }

    public function test_sisip_rumus_opens_widget_and_inserts_latex(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->set('soal', 'Hitung')
            ->call('bukaRumusWidget')
            ->assertSee('Editor rumus')
            ->assertSee('ck-math-widget', false)
            ->call('sisipLatex', '\\frac{1}{2}')
            ->assertSet('soal', 'Hitung $\\frac{1}{2}$')
            ->assertDontSee('Editor rumus')
            ->set('opsi.A', 'Nilai')
            ->call('bukaRumusWidget', 'A')
            ->assertSee('opsi A')
            ->call('sisipLatex', 'x^{2}')
            ->assertSet('opsi.A', 'Nilai $x^{2}$');
    }

    public function test_panduan_rumus_modal_shows_steps(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->assertSee('Cara sisipkan rumus')
            ->assertDontSee('Ketik rumus LaTeX')
            ->call('bukaPanduanRumus')
            ->assertSee('Editor rumus akan terbuka')
            ->assertSee('Sisip rumus')
            ->call('tutupPanduanRumus')
            ->assertDontSee('Editor rumus akan terbuka');
    }

    public function test_opsi_can_store_equation_and_image(): void
    {
        Storage::fake('public');
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru);
        $file = UploadedFile::fake()->image('opsi-a.png', 30, 30);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->set('soal', 'Pilih rumus yang benar')
            ->set('poin', '5')
            ->set('kunci', 'A')
            ->set('opsi.A', '$\frac{1}{2}$')
            ->set('opsi.B', '$2$')
            ->set('opsi.C', '$3$')
            ->set('opsi.D', '$4$')
            ->set('gambarOpsi.A', $file)
            ->call('simpan')
            ->assertHasNoErrors();

        $opsiA = BankOpsi::query()->where('kode', 'A')->firstOrFail();
        $this->assertSame('$\frac{1}{2}$', $opsiA->teks);
        $this->assertNotNull($opsiA->gambar);
        Storage::disk('public')->assertExists($opsiA->gambar);
    }

    public function test_gambar_insertion_hidden_until_toggled_for_both_bentuk(): void
    {
        $guru = $this->guruFixture();

        foreach ([BankSoalBentuk::MATEMATIS, BankSoalBentuk::BIASA] as $bentuk) {
            $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL, 'Paket '.$bentuk, $bentuk);

            Livewire::actingAs($guru)
                ->test(BankSoalForm::class, ['paket' => $paket->id])
                ->call('bukaForm')
                ->assertSee('Sisip gambar')
                ->assertDontSee('Gambar soal')
                ->assertDontSee('Gambar opsi')
                ->assertDontSee('id="gambar"', false)
                ->assertSee('Potong gambar')
                ->assertSee('Kecil (640 px)')
                ->assertSee('Sedang (960 px)')
                ->assertSee('Besar (1200 px)')
                ->call('toggleGambarSoal')
                ->assertSee('Gambar soal')
                ->assertSee('id="gambar"', false)
                ->call('toggleGambarSoal')
                ->assertDontSee('id="gambar"', false)
                ->call('toggleGambarOpsi', 'A')
                ->assertSee('Gambar opsi')
                ->assertSee('id="gambar-opsi-A"', false)
                ->call('toggleGambarOpsi', 'A')
                ->assertDontSee('id="gambar-opsi-A"', false);
        }
    }

    public function test_edit_with_existing_image_opens_gambar_panel(): void
    {
        Storage::fake('public');
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL, 'PKN', BankSoalBentuk::BIASA);
        $file = UploadedFile::fake()->image('soal.png', 40, 40);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->set('soal', 'Perhatikan gambar')
            ->set('poin', '5')
            ->set('kunci', 'A')
            ->set('opsi.A', 'Ya')
            ->set('opsi.B', 'Tidak')
            ->set('opsi.C', 'Ragu')
            ->set('opsi.D', 'Kosong')
            ->set('gambar', $file)
            ->set('gambarOpsi.B', UploadedFile::fake()->image('opsi-b.png', 20, 20))
            ->call('simpan')
            ->assertHasNoErrors();

        $id = BankSoal::firstOrFail()->id;

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('ubah', $id)
            ->assertSee('id="gambar"', false)
            ->assertSee('Hapus gambar')
            ->assertSee('id="gambar-opsi-B"', false)
            ->assertDontSee('id="gambar-opsi-A"', false);
    }

    public function test_biasa_paket_hides_formula_conversion(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL, 'PKN', BankSoalBentuk::BIASA);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->call('bukaForm')
            ->assertDontSee('Sisip rumus')
            ->assertDontSee('Cara sisipkan rumus')
            ->assertDontSee('Editor rumus')
            ->set('soal', 'Hitung')
            ->call('sisipLatex', '\\frac{1}{2}')
            ->assertSet('soal', 'Hitung')
            ->set('poin', '5')
            ->set('kunci', 'A')
            ->set('opsi.A', 'Ya')
            ->set('opsi.B', 'Tidak')
            ->set('opsi.C', 'Ragu')
            ->set('opsi.D', 'Kosong')
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertSame('Hitung', BankSoal::firstOrFail()->soal);
    }

    public function test_paket_page_offers_excel_template_and_import(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->assertSee('Impor Excel')
            ->assertDontSee('Unduh template')
            ->call('bukaImpor')
            ->assertSee('Unduh template')
            ->assertSee('File Excel')
            ->assertSee(route('pendidik.cat.bank-soal.template', $paket, false), false)
            ->call('tutupImpor')
            ->assertDontSee('Unduh template');
    }

    public function test_pendidik_can_download_tunggal_excel_template(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL);

        $response = $this->actingAs($guru)
            ->get(route('pendidik.cat.bank-soal.template', $paket));

        $response->assertOk()->assertDownload('template-soal-tunggal.xlsx');

        $sheet = IOFactory::load($response->getFile()->getPathname())->getActiveSheet()->toArray();
        $this->assertSame(['soal', 'poin', 'kunci', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'], $sheet[0]);
        $this->assertSame('Hasil dari 1+1 adalah', $sheet[1][0]);
        $this->assertSame(10, (int) $sheet[1][1]);
        $this->assertSame('B', $sheet[1][2]);
    }

    public function test_pendidik_can_download_pembobotan_excel_template(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::PEMBOBOTAN, 'Sikap');

        $response = $this->actingAs($guru)
            ->get(route('pendidik.cat.bank-soal.template', $paket));

        $response->assertOk()->assertDownload('template-soal-pembobotan.xlsx');

        $sheet = IOFactory::load($response->getFile()->getPathname())->getActiveSheet()->toArray();
        $this->assertSame([
            'soal', 'opsi_a', 'poin_a', 'opsi_b', 'poin_b', 'opsi_c', 'poin_c', 'opsi_d', 'poin_d', 'opsi_e', 'poin_e',
        ], $sheet[0]);
        $this->assertSame('Sikap yang paling tepat', $sheet[1][0]);
        $this->assertSame(5, (int) $sheet[1][2]);
        $this->assertSame(0, (int) $sheet[1][8]);
        $this->assertSame(-1, (int) $sheet[1][10]);
    }

    public function test_cannot_download_other_pendidik_template(): void
    {
        $guru = $this->guruFixture();
        $lain = User::factory()->create(['role_id' => 3]);
        Pendidik::create(['pendidik_id' => $lain->id]);
        $paketLain = $this->paketFixture($lain, BankSoalTipe::TUNGGAL, 'Rahasia');

        $this->actingAs($guru)
            ->get(route('pendidik.cat.bank-soal.template', $paketLain))
            ->assertNotFound();
    }

    public function test_pendidik_imports_tunggal_questions_from_excel(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->set('fileImpor', $this->excelFile([
                ['soal', 'poin', 'kunci', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'],
                ['Hasil dari 2+2', 10, 'b', '2', '3', '4', '5', ''],
                ['Ibu kota Indonesia', 5, 'A', 'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Makassar'],
            ]))
            ->call('impor')
            ->assertHasNoErrors()
            ->assertSee('2 soal diimpor');

        $this->assertSame(2, BankSoal::count());
        $pertama = BankSoal::query()->where('soal', 'Hasil dari 2+2')->firstOrFail();
        $this->assertSame($paket->id, (int) $pertama->paket_id);
        $this->assertSame($guru->id, (int) $pertama->pendidik_id);
        $this->assertSame(10, (int) $pertama->poin);
        $this->assertSame('B', $pertama->kunci);
        $this->assertSame(['A', 'B', 'C', 'D'], $pertama->opsi()->pluck('kode')->all());
        $this->assertSame('Jakarta', BankSoal::query()->where('soal', 'Ibu kota Indonesia')->firstOrFail()->opsi()->where('kode', 'A')->value('teks'));
    }

    public function test_pendidik_imports_pembobotan_questions_from_excel(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::PEMBOBOTAN, 'Sikap');

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->set('fileImpor', $this->excelFile([
                ['soal', 'opsi_a', 'poin_a', 'opsi_b', 'poin_b', 'opsi_c', 'poin_c', 'opsi_d', 'poin_d', 'opsi_e', 'poin_e'],
                ['Sikap yang paling tepat', 'Menolong', 5, 'Diam', 3, 'Menyindir', 1, 'Mengabaikan', 0, 'Mengejek', -1],
            ]))
            ->call('impor')
            ->assertHasNoErrors()
            ->assertSee('1 soal diimpor');

        $row = BankSoal::firstOrFail();
        $this->assertNull($row->kunci);
        $this->assertNull($row->poin);
        $this->assertSame(5, (int) $row->opsi()->where('kode', 'A')->value('poin'));
        $this->assertSame(-1, (int) $row->opsi()->where('kode', 'E')->value('poin'));
    }

    public function test_invalid_excel_row_does_not_import_any_question(): void
    {
        $guru = $this->guruFixture();
        $paket = $this->paketFixture($guru, BankSoalTipe::TUNGGAL);

        Livewire::actingAs($guru)
            ->test(BankSoalForm::class, ['paket' => $paket->id])
            ->set('fileImpor', $this->excelFile([
                ['soal', 'poin', 'kunci', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'],
                ['Soal valid', 10, 'A', '1', '2', '3', '4', ''],
                ['', 10, 'A', '1', '2', '3', '4', ''],
                ['Tanpa opsi', 10, 'A', '', '2', '3', '4', ''],
            ]))
            ->call('impor')
            ->assertHasErrors('fileImpor');

        $this->assertSame(0, BankSoal::count());
    }

    private function excelFile(array $rows, string $name = 'soal.xlsx'): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A1', true);
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('bank-soal-', true).'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return UploadedFile::fake()->createWithContent($name, (string) file_get_contents($path));
    }

    private function paketFixture(User $guru, string $tipe = BankSoalTipe::TUNGGAL, string $nama = 'Paket Uji', string $bentuk = BankSoalBentuk::MATEMATIS): BankPaket
    {
        return BankPaket::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $guru->pendidik?->mapel_id,
            'nama' => $nama,
            'tipe' => $tipe,
            'bentuk' => $bentuk,
        ]);
    }

    private function guruFixture(): User
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $mapel = Mapel::create(['mapel' => 'Matematika']);
        $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Bank']);
        Pendidik::create([
            'pendidik_id' => $guru->id,
            'mapel_id' => $mapel->id,
            'markas_id' => $markas->id,
        ]);

        return $guru->fresh();
    }
}
