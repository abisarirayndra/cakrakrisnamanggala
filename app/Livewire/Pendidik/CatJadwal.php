<?php

namespace App\Livewire\Pendidik;

use App\BankPaket;
use App\CatJadwal as Jadwal;
use App\Support\BankSoalTipe;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-pendidik')]
#[Title('Jadwal CAT')]
class CatJadwal extends Component
{
    public bool $formTerbuka = false;

    public string $nama = '';

    public string $bank_paket_id = '';

    public string $mulai = '';

    public string $selesai = '';

    public array $bankTerpilih = [];

    public ?int $editId = null;

    public ?int $chatId = null;

    public string $teksChat = '';

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
        $row = $this->jadwalMilik($id)->load(['banks.mapel']);

        $this->resetErrorBag();
        $this->editId = $row->id;
        $this->nama = $row->nama;
        $this->bank_paket_id = '';
        $this->mulai = $row->mulai?->format('Y-m-d\TH:i') ?? '';
        $this->selesai = $row->selesai?->format('Y-m-d\TH:i') ?? '';
        $this->bankTerpilih = $row->banks->map(fn ($bank) => $this->barisBank($bank))->all();
        $this->formTerbuka = true;
    }

    public function tambahBank(): void
    {
        $this->validate([
            'bank_paket_id' => [
                'required',
                Rule::exists('cat_bank_paket', 'id')->where('pendidik_id', auth()->id()),
            ],
        ]);

        $id = (int) $this->bank_paket_id;
        if (collect($this->bankTerpilih)->contains(fn (array $row) => (int) $row['id'] === $id)) {
            $this->bank_paket_id = '';

            return;
        }

        $bank = $this->bankMilik()->with('mapel')->withCount('soal')->findOrFail($id);

        $this->bankTerpilih[] = $this->barisBank($bank);
        $this->bank_paket_id = '';
        $this->resetErrorBag('bankTerpilih');
    }

    public function hapusBank(int $index): void
    {
        unset($this->bankTerpilih[$index]);
        $this->bankTerpilih = array_values($this->bankTerpilih);
    }

    public function simpan(): void
    {
        $this->validate([
            'nama' => 'required|max:191',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
            'bankTerpilih' => 'required|array|min:1',
        ]);

        $ids = collect($this->bankTerpilih)->pluck('id')->map(fn ($id) => (int) $id)->unique()->values();
        $milik = $this->bankMilik()->whereIn('id', $ids)->pluck('id')->map(fn ($id) => (int) $id);

        if ($milik->sort()->values()->all() !== $ids->sort()->values()->all()) {
            $this->addError('bankTerpilih', 'Bank soal tidak valid.');

            return;
        }

        $payload = [
            'nama' => trim($this->nama),
            'mulai' => $this->mulai,
            'selesai' => $this->selesai,
        ];

        if ($this->editId !== null) {
            $row = $this->jadwalMilik($this->editId);
            $row->update($payload);
        } else {
            $row = Jadwal::create($payload + [
                'admin_id' => auth()->id(),
                'token' => $this->buatTokenUnik(),
            ]);
        }

        $row->pasangBanks($ids);
        $this->batal();
    }

    public function perbaruiToken(int $id): void
    {
        $row = $this->jadwalMilik($id);
        $row->update(['token' => $this->buatTokenUnik()]);

        if ($this->chatId === $id) {
            $this->teksChat = $row->fresh()->teksChat();
        }
    }

    public function lihatChat(int $id): void
    {
        $row = $this->jadwalMilik($id);
        $this->chatId = $row->id;
        $this->teksChat = $row->teksChat();
    }

    public function tutupChat(): void
    {
        $this->chatId = null;
        $this->teksChat = '';
    }

    public function hapus(int $id): void
    {
        $row = $this->jadwalMilik($id);
        $row->banks()->detach();
        $row->delete();

        if ($this->editId === $id) {
            $this->batal();
        }

        if ($this->chatId === $id) {
            $this->tutupChat();
        }
    }

    public function render()
    {
        $terpakai = collect($this->bankTerpilih)->pluck('id')->all();

        $bankList = $this->bankMilik()
            ->with('mapel')
            ->withCount('soal')
            ->when($terpakai !== [], fn ($query) => $query->whereNotIn('id', $terpakai))
            ->orderBy('nama')
            ->get();

        return view('livewire.pendidik.cat-jadwal', [
            'bankList' => $bankList,
            'daftar' => Jadwal::query()
                ->where('admin_id', auth()->id())
                ->with(['banks.mapel'])
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    private function bankMilik()
    {
        return BankPaket::query()->where('pendidik_id', auth()->id());
    }

    private function jadwalMilik(int $id): Jadwal
    {
        return Jadwal::query()
            ->where('admin_id', auth()->id())
            ->whereKey($id)
            ->firstOrFail();
    }

    private function buatTokenUnik(): string
    {
        do {
            $token = Str::upper(Str::random(6));
        } while (Jadwal::query()->where('token', $token)->exists());

        return $token;
    }

    private function barisBank($bank): array
    {
        return [
            'id' => $bank->id,
            'nama' => $bank->nama,
            'mapel' => $bank->mapel?->mapel,
            'tipe' => BankSoalTipe::label($bank->tipe),
            'soal_count' => $bank->soal_count ?? $bank->soal()->count(),
        ];
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->editId = null;
        $this->nama = '';
        $this->bank_paket_id = '';
        $this->mulai = '';
        $this->selesai = '';
        $this->bankTerpilih = [];
    }
}
