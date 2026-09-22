<?php

namespace App\Livewire\Admin;

use App\CatJadwal as Jadwal;
use App\Support\AdminVisibility;
use App\Support\BankSoalTipe;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-cakra')]
#[Title('Jadwal CAT')]
class CatJadwal extends Component
{
    public bool $formTerbuka = false;

    public string $nama = '';

    public string $pendidik_id = '';

    public string $bank_paket_id = '';

    public string $mulai = '';

    public string $selesai = '';

    public array $bankTerpilih = [];

    public ?int $editId = null;

    public ?int $chatId = null;

    public string $teksChat = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
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
        $row = $this->jadwalTerlihat($id)->load(['banks.pendidik']);

        $this->resetErrorBag();
        $this->editId = $row->id;
        $this->nama = $row->nama;
        $this->pendidik_id = (string) ($row->banks->first()?->pendidik_id ?? '');
        $this->bank_paket_id = '';
        $this->mulai = $row->mulai?->format('Y-m-d\TH:i') ?? '';
        $this->selesai = $row->selesai?->format('Y-m-d\TH:i') ?? '';
        $this->bankTerpilih = $row->banks->map(fn ($bank) => $this->barisBank($bank))->all();
        $this->formTerbuka = true;
    }

    public function updatedPendidikId(): void
    {
        if ($this->bank_paket_id === '') {
            return;
        }

        $cocok = AdminVisibility::bankPaketQuery(auth()->user())
            ->whereKey($this->bank_paket_id)
            ->where('pendidik_id', $this->pendidik_id)
            ->exists();

        if (! $cocok) {
            $this->bank_paket_id = '';
        }
    }

    public function tambahBank(): void
    {
        $actor = auth()->user();
        $pendidikIds = AdminVisibility::pendidikQuery($actor)->pluck('users.id')->map(fn ($id) => (string) $id)->all();

        $this->validate([
            'pendidik_id' => ['required', Rule::in($pendidikIds)],
            'bank_paket_id' => [
                'required',
                Rule::exists('cat_bank_paket', 'id')->where('pendidik_id', $this->pendidik_id),
            ],
        ]);

        $id = (int) $this->bank_paket_id;
        if (collect($this->bankTerpilih)->contains(fn (array $row) => (int) $row['id'] === $id)) {
            $this->bank_paket_id = '';

            return;
        }

        $bank = AdminVisibility::bankPaketQuery($actor)
            ->with('pendidik')
            ->withCount('soal')
            ->findOrFail($id);

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
        $actor = auth()->user();

        $this->validate([
            'nama' => 'required|max:191',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
            'bankTerpilih' => 'required|array|min:1',
        ]);

        $ids = collect($this->bankTerpilih)->pluck('id')->map(fn ($id) => (int) $id)->unique()->values();
        $terlihat = AdminVisibility::bankPaketQuery($actor)->whereIn('id', $ids)->pluck('id')->map(fn ($id) => (int) $id);

        if ($terlihat->sort()->values()->all() !== $ids->sort()->values()->all()) {
            $this->addError('bankTerpilih', 'Bank soal tidak valid.');

            return;
        }

        $payload = [
            'nama' => trim($this->nama),
            'mulai' => $this->mulai,
            'selesai' => $this->selesai,
        ];

        if ($this->editId !== null) {
            $row = $this->jadwalTerlihat($this->editId);
            $row->update($payload);
        } else {
            $row = Jadwal::create($payload + [
                'admin_id' => $actor->id,
                'token' => $this->buatTokenUnik(),
            ]);
        }

        $row->pasangBanks($ids);
        $this->batal();
    }

    public function perbaruiToken(int $id): void
    {
        $row = $this->jadwalTerlihat($id);
        $row->update(['token' => $this->buatTokenUnik()]);

        if ($this->chatId === $id) {
            $this->teksChat = $row->fresh()->teksChat();
        }
    }

    public function lihatChat(int $id): void
    {
        $row = $this->jadwalTerlihat($id);
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
        $row = $this->jadwalTerlihat($id);
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
        $actor = auth()->user();
        $terpakai = collect($this->bankTerpilih)->pluck('id')->all();

        $bankList = $this->pendidik_id === ''
            ? collect()
            :             AdminVisibility::bankPaketQuery($actor)
                ->with('mapel')
                ->withCount('soal')
                ->where('pendidik_id', $this->pendidik_id)
                ->when($terpakai !== [], fn ($query) => $query->whereNotIn('id', $terpakai))
                ->orderBy('nama')
                ->get();

        return view('livewire.admin.cat-jadwal', [
            'pendidikList' => AdminVisibility::pendidikQuery($actor)->orderBy('users.nama')->get(),
            'bankList' => $bankList,
            'daftar' => AdminVisibility::catJadwalQuery($actor)
                ->with(['banks.pendidik', 'banks.mapel'])
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    private function jadwalTerlihat(int $id): Jadwal
    {
        return AdminVisibility::catJadwalQuery(auth()->user())
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
            'pendidik' => $bank->pendidik?->nama,
            'tipe' => BankSoalTipe::label($bank->tipe),
            'soal_count' => $bank->soal_count ?? $bank->soal()->count(),
        ];
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->editId = null;
        $this->nama = '';
        $this->pendidik_id = '';
        $this->bank_paket_id = '';
        $this->mulai = '';
        $this->selesai = '';
        $this->bankTerpilih = [];
    }
}
