<?php

namespace App\Livewire\Pendidik;

use App\BankOpsi;
use App\BankPaket;
use App\BankSoal;
use App\Imports\BankSoalImport;
use App\Support\BankSoalTipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.panel-pendidik')]
#[Title('Bank Soal')]
class BankSoalForm extends Component
{
    use WithFileUploads;

    public int $paketId;

    public string $cari = '';

    public bool $formTerbuka = false;

    public bool $showPanduanRumus = false;

    public bool $showRumusWidget = false;

    public bool $showImpor = false;

    public ?string $rumusTarget = null;

    public ?int $editId = null;

    public string $soal = '';

    public $gambar = null;

    public ?string $gambarLama = null;

    public string $poin = '';

    public string $kunci = '';

    public array $opsi = [
        'A' => '',
        'B' => '',
        'C' => '',
        'D' => '',
        'E' => '',
    ];

    public array $poinOpsi = [
        'A' => '',
        'B' => '',
        'C' => '',
        'D' => '',
        'E' => '',
    ];

    public $gambarOpsi = [
        'A' => null,
        'B' => null,
        'C' => null,
        'D' => null,
        'E' => null,
    ];

    public array $gambarOpsiLama = [
        'A' => null,
        'B' => null,
        'C' => null,
        'D' => null,
        'E' => null,
    ];

    public bool $showGambarSoal = false;

    public array $showGambarOpsi = [
        'A' => false,
        'B' => false,
        'C' => false,
        'D' => false,
        'E' => false,
    ];

    public $fileImpor = null;

    public string $pesanImpor = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->isPengajar(), 403);
    }

    public function mount(int $paket): void
    {
        $this->paketId = $this->paketMilikSendiri($paket)->id;
    }

    public function bukaForm(): void
    {
        $this->tutupImpor();
        $this->resetForm();
        $this->formTerbuka = true;
    }

    public function batal(): void
    {
        $this->resetForm();
        $this->formTerbuka = false;
        $this->showPanduanRumus = false;
        $this->tutupRumusWidget();
    }

    public function bukaImpor(): void
    {
        $this->resetErrorBag('fileImpor');
        $this->reset('fileImpor');
        $this->showImpor = true;
    }

    public function tutupImpor(): void
    {
        $this->showImpor = false;
        $this->reset('fileImpor');
        $this->resetErrorBag('fileImpor');
    }

    public function bukaPanduanRumus(): void
    {
        if (! $this->paketAktif()->isMatematis()) {
            return;
        }

        $this->showPanduanRumus = true;
    }

    public function tutupPanduanRumus(): void
    {
        $this->showPanduanRumus = false;
    }

    public function bukaRumusWidget(?string $kode = null): void
    {
        if (! $this->paketAktif()->isMatematis()) {
            return;
        }

        $this->resetErrorBag('rumus');
        $this->rumusTarget = $kode ?: null;
        $this->showRumusWidget = true;
    }

    public function tutupRumusWidget(): void
    {
        $this->showRumusWidget = false;
        $this->rumusTarget = null;
        $this->resetErrorBag('rumus');
    }

    public function sisipRumus(?string $kode = null): void
    {
        $this->bukaRumusWidget($kode);
    }

    public function sisipLatex(string $latex): void
    {
        if (! $this->paketAktif()->isMatematis()) {
            return;
        }

        $latex = $this->bersihkanLatex($latex);

        if ($latex === '') {
            $this->addError('rumus', 'Rumus masih kosong.');

            return;
        }

        $snippet = ' $'.$latex.'$';
        $kode = $this->rumusTarget;

        if ($kode === null || $kode === '') {
            $this->soal = rtrim($this->soal).$snippet;
        } elseif (array_key_exists($kode, $this->opsi)) {
            $this->opsi[$kode] = rtrim((string) $this->opsi[$kode]).$snippet;
        }

        $this->tutupRumusWidget();
    }

    public function toggleGambarSoal(): void
    {
        $this->showGambarSoal = ! $this->showGambarSoal;
    }

    public function toggleGambarOpsi(string $kode): void
    {
        if (! array_key_exists($kode, $this->showGambarOpsi)) {
            return;
        }

        $this->showGambarOpsi[$kode] = ! $this->showGambarOpsi[$kode];
    }

    public function updatedGambar(): void
    {
        $this->showGambarSoal = true;
    }

    public function updatedGambarOpsi($value, string $kode): void
    {
        if (array_key_exists($kode, $this->showGambarOpsi)) {
            $this->showGambarOpsi[$kode] = true;
        }
    }

    public function impor(): void
    {
        $this->pesanImpor = '';
        $this->validate([
            'fileImpor' => 'required|file|mimes:xlsx,xls',
        ]);

        $impor = new BankSoalImport($this->paketAktif(), auth()->user());

        try {
            Excel::import($impor, $this->fileImpor->getRealPath());
        } catch (ValidationException $e) {
            $this->reset('fileImpor');
            throw $e;
        } catch (\Throwable) {
            $this->reset('fileImpor');
            $this->addError('fileImpor', 'File Excel tidak bisa dibaca. Unduh template dan coba lagi.');

            return;
        }

        $this->reset('fileImpor');
        $this->showImpor = false;
        $this->pesanImpor = $impor->jumlah.' soal diimpor.';
    }

    public function simpan(): void
    {
        $paket = $this->paketAktif();
        $tipe = $paket->tipe;

        $rules = [
            'soal' => 'required',
            'opsi.A' => 'required',
            'opsi.B' => 'required',
            'opsi.C' => 'required',
            'opsi.D' => 'required',
            'opsi.E' => 'nullable',
            'gambar' => 'nullable|image|max:2048',
            'gambarOpsi.A' => 'nullable|image|max:2048',
            'gambarOpsi.B' => 'nullable|image|max:2048',
            'gambarOpsi.C' => 'nullable|image|max:2048',
            'gambarOpsi.D' => 'nullable|image|max:2048',
            'gambarOpsi.E' => 'nullable|image|max:2048',
        ];

        if ($tipe === BankSoalTipe::TUNGGAL) {
            $rules['poin'] = 'required|integer|min:0';
            $rules['kunci'] = 'required|in:A,B,C,D,E';
        } else {
            $rules['poinOpsi.A'] = 'required|integer';
            $rules['poinOpsi.B'] = 'required|integer';
            $rules['poinOpsi.C'] = 'required|integer';
            $rules['poinOpsi.D'] = 'required|integer';
            $rules['poinOpsi.E'] = filled($this->opsi['E'] ?? '') ? 'required|integer' : 'nullable|integer';
        }

        $this->validate($rules);

        if ($tipe === BankSoalTipe::TUNGGAL && $this->kunci === 'E' && ! filled($this->opsi['E'] ?? '')) {
            $this->addError('kunci', 'Opsi E harus diisi jika menjadi kunci.');

            return;
        }

        $actor = auth()->user();
        $path = $this->gambarLama;

        if ($this->gambar) {
            if ($this->gambarLama) {
                Storage::disk('public')->delete($this->gambarLama);
            }
            $path = $this->gambar->store('cat/soal/'.$actor->id, 'public');
        }

        $row = $this->editId
            ? $this->soalMilikSendiri($this->editId)
            : new BankSoal;

        $row->fill([
            'paket_id' => $paket->id,
            'pendidik_id' => $actor->id,
            'mapel_id' => $actor->pendidik?->mapel_id,
            'soal' => $this->soal,
            'gambar' => $path,
            'poin' => $tipe === BankSoalTipe::TUNGGAL ? (int) $this->poin : null,
            'kunci' => $tipe === BankSoalTipe::TUNGGAL ? $this->kunci : null,
        ]);
        $row->save();

        $lama = $row->opsi()->get()->keyBy('kode');
        $row->opsi()->delete();
        $dipakai = [];

        foreach (['A', 'B', 'C', 'D', 'E'] as $urutan => $kode) {
            $teks = trim((string) ($this->opsi[$kode] ?? ''));
            $gambarPath = $this->simpanGambarOpsi($kode, $actor->id);
            if ($teks === '' && $gambarPath === null) {
                continue;
            }

            BankOpsi::create([
                'bank_soal_id' => $row->id,
                'kode' => $kode,
                'teks' => $teks,
                'gambar' => $gambarPath,
                'poin' => $tipe === BankSoalTipe::PEMBOBOTAN ? (int) $this->poinOpsi[$kode] : 0,
                'urutan' => $urutan + 1,
            ]);

            if ($gambarPath) {
                $dipakai[] = $gambarPath;
            }
        }

        foreach ($lama as $item) {
            if ($item->gambar && ! in_array($item->gambar, $dipakai, true)) {
                Storage::disk('public')->delete($item->gambar);
            }
        }

        $this->resetForm();
        $this->formTerbuka = false;
    }

    public function ubah(int $id): void
    {
        $row = $this->soalMilikSendiri($id)->load('opsi');

        $this->resetForm();
        $this->editId = $row->id;
        $this->soal = $row->soal;
        $this->gambarLama = $row->gambar;
        $this->showGambarSoal = filled($row->gambar);
        $this->poin = $row->poin === null ? '' : (string) $row->poin;
        $this->kunci = (string) ($row->kunci ?? '');

        foreach ($row->opsi as $item) {
            $this->opsi[$item->kode] = $item->teks;
            $this->poinOpsi[$item->kode] = (string) $item->poin;
            $this->gambarOpsiLama[$item->kode] = $item->gambar;
            $this->showGambarOpsi[$item->kode] = filled($item->gambar);
        }

        $this->formTerbuka = true;
    }

    public function hapusGambar(): void
    {
        if ($this->gambarLama) {
            Storage::disk('public')->delete($this->gambarLama);
        }

        $this->gambarLama = null;
        $this->gambar = null;

        if ($this->editId) {
            $this->soalMilikSendiri($this->editId)->update(['gambar' => null]);
        }
    }

    public function hapusGambarOpsi(string $kode): void
    {
        if (! array_key_exists($kode, $this->gambarOpsiLama)) {
            return;
        }

        if ($this->gambarOpsiLama[$kode]) {
            Storage::disk('public')->delete($this->gambarOpsiLama[$kode]);
        }

        $this->gambarOpsiLama[$kode] = null;
        $this->gambarOpsi[$kode] = null;

        if ($this->editId) {
            BankOpsi::query()
                ->where('bank_soal_id', $this->editId)
                ->where('kode', $kode)
                ->update(['gambar' => null]);
        }
    }

    public function hapus(int $id): void
    {
        $row = $this->soalMilikSendiri($id)->load('opsi');
        if ($row->gambar) {
            Storage::disk('public')->delete($row->gambar);
        }
        foreach ($row->opsi as $opsi) {
            if ($opsi->gambar) {
                Storage::disk('public')->delete($opsi->gambar);
            }
        }
        $row->opsi()->delete();
        $row->delete();

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function render()
    {
        $paket = $this->paketAktif();

        $daftar = BankSoal::query()
            ->with('opsi')
            ->where('paket_id', $paket->id)
            ->when($this->cari !== '', fn ($query) => $query->where('soal', 'like', '%'.$this->cari.'%'))
            ->orderByDesc('id')
            ->get();

        return view('livewire.pendidik.bank-soal', [
            'paket' => $paket,
            'daftar' => $daftar,
        ]);
    }

    private function simpanGambarOpsi(string $kode, int $userId): ?string
    {
        $baru = $this->gambarOpsi[$kode] ?? null;
        $lama = $this->gambarOpsiLama[$kode] ?? null;

        if ($baru instanceof TemporaryUploadedFile) {
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }

            return $baru->store('cat/soal/'.$userId.'/opsi', 'public');
        }

        return $lama ?: null;
    }

    private function paketAktif(): BankPaket
    {
        return $this->paketMilikSendiri($this->paketId);
    }

    private function paketMilikSendiri(int $id): BankPaket
    {
        return BankPaket::query()
            ->where('pendidik_id', auth()->id())
            ->whereKey($id)
            ->firstOrFail();
    }

    private function soalMilikSendiri(int $id): BankSoal
    {
        return BankSoal::query()
            ->where('pendidik_id', auth()->id())
            ->where('paket_id', $this->paketId)
            ->whereKey($id)
            ->firstOrFail();
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->editId = null;
        $this->soal = '';
        $this->gambar = null;
        $this->gambarLama = null;
        $this->poin = '';
        $this->kunci = '';
        $this->opsi = ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''];
        $this->poinOpsi = ['A' => '', 'B' => '', 'C' => '', 'D' => '', 'E' => ''];
        $this->gambarOpsi = ['A' => null, 'B' => null, 'C' => null, 'D' => null, 'E' => null];
        $this->gambarOpsiLama = ['A' => null, 'B' => null, 'C' => null, 'D' => null, 'E' => null];
        $this->showGambarSoal = false;
        $this->showGambarOpsi = ['A' => false, 'B' => false, 'C' => false, 'D' => false, 'E' => false];
        $this->showRumusWidget = false;
        $this->rumusTarget = null;
    }

    private function bersihkanLatex(string $latex): string
    {
        $latex = trim(str_replace("\0", '', $latex));
        $latex = trim($latex, '$');
        $latex = preg_replace('/\s+/', ' ', $latex) ?? '';

        return trim($latex);
    }
}
