<?php

namespace App\Livewire\Pendidik;

use App\BankPaket as Paket;
use App\Support\BankSoalBentuk;
use App\Support\BankSoalTipe;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-pendidik')]
#[Title('Bank Soal')]
class BankPaket extends Component
{
    public bool $formTerbuka = false;

    public string $nama = '';

    public string $tipe = BankSoalTipe::TUNGGAL;

    public string $bentuk = BankSoalBentuk::BIASA;

    public ?int $editId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isPengajar(), 403);
    }

    public function bukaForm(): void
    {
        $this->resetForm();
        $this->formTerbuka = true;
    }

    public function batal(): void
    {
        $this->resetForm();
        $this->formTerbuka = false;
    }

    public function ubah(int $id): void
    {
        $paket = $this->milikSendiri($id);

        $this->resetErrorBag();
        $this->editId = $paket->id;
        $this->nama = $paket->nama;
        $this->tipe = $paket->tipe;
        $this->bentuk = $paket->bentuk ?: BankSoalBentuk::BIASA;
        $this->formTerbuka = true;
    }

    public function simpan(): void
    {
        $this->validate([
            'nama' => 'required|max:191',
            'tipe' => 'required|in:'.BankSoalTipe::TUNGGAL.','.BankSoalTipe::PEMBOBOTAN,
            'bentuk' => 'required|in:'.BankSoalBentuk::MATEMATIS.','.BankSoalBentuk::BIASA,
        ]);

        $actor = auth()->user();
        $payload = [
            'nama' => trim($this->nama),
            'tipe' => $this->tipe,
            'bentuk' => $this->bentuk,
        ];

        if ($this->editId !== null) {
            $this->milikSendiri($this->editId)->update($payload);
        } else {
            Paket::create($payload + [
                'pendidik_id' => $actor->id,
                'mapel_id' => $actor->pendidik?->mapel_id,
            ]);
        }

        $this->batal();
    }

    public function hapus(int $id): void
    {
        $paket = $this->milikSendiri($id)->load(['soal.opsi']);

        foreach ($paket->soal as $soal) {
            if ($soal->gambar) {
                Storage::disk('public')->delete($soal->gambar);
            }
            foreach ($soal->opsi as $opsi) {
                if ($opsi->gambar) {
                    Storage::disk('public')->delete($opsi->gambar);
                }
            }
            $soal->opsi()->delete();
            $soal->delete();
        }

        $paket->delete();

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function render()
    {
        $daftar = Paket::query()
            ->withCount('soal')
            ->where('pendidik_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        return view('livewire.pendidik.bank-paket', [
            'daftar' => $daftar,
        ]);
    }

    private function milikSendiri(int $id): Paket
    {
        return Paket::query()
            ->where('pendidik_id', auth()->id())
            ->whereKey($id)
            ->firstOrFail();
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->editId = null;
        $this->nama = '';
        $this->tipe = BankSoalTipe::TUNGGAL;
        $this->bentuk = BankSoalBentuk::BIASA;
    }
}
