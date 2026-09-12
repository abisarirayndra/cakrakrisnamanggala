<?php

namespace App\Livewire\Admin;

use App\Mapel;
use App\Markas;
use App\Pendidik;
use App\Support\AdminVisibility;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Pendidik')]
class MasterPendidik extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $cari = '';

    public string $halaman = 'daftar';

    public string $nama = '';

    public string $email = '';

    public $markas_id = '';

    public $mapel_id = '';

    public $nik = '';

    public $nip = '';

    public $tempat_lahir = '';

    public $tanggal_lahir = '';

    public $alamat = '';

    public $wa = '';

    public $ibu = '';

    public ?int $lihatId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $this->prefillMarkas();
    }

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function tambah(): void
    {
        $actor = auth()->user();
        $markasRules = ['required', 'integer', 'exists:adm_markas,id'];

        if (! $actor->isSuperAdmin()) {
            $markasRules[] = Rule::in($actor->markasIds());
        }

        $validated = $this->validate([
            'nama' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'markas_id' => $markasRules,
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make(Pendidik::DEFAULT_PASSWORD),
                'role_id' => 3,
            ]);

            Pendidik::create([
                'pendidik_id' => $user->id,
                'mapel_id' => 10,
                'markas_id' => (int) $validated['markas_id'],
            ]);
        });

        $this->reset(['nama', 'email']);
        $this->resetErrorBag();
        $this->resetPage();
        $this->prefillMarkas();
    }

    public function hapus(int $userId): void
    {
        $this->authorizeRow($userId);
        User::where('role_id', 3)->findOrFail($userId)->delete();

        if ($this->lihatId === $userId) {
            $this->kembali();
        }
    }

    public function lihat(int $userId): void
    {
        $this->authorizeRow($userId);
        $this->lihatId = $userId;
        $this->halaman = 'lihat';
    }

    public function edit(int $userId): void
    {
        $pendidik = $this->authorizeRow($userId);
        $user = User::where('role_id', 3)->findOrFail($userId);

        $this->lihatId = $userId;
        $this->halaman = 'edit';
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->nik = $pendidik->nik ?? '';
        $this->nip = $pendidik->nip ?? '';
        $this->tempat_lahir = $pendidik->tempat_lahir ?? '';
        $this->tanggal_lahir = optional($pendidik->tanggal_lahir)->format('Y-m-d') ?? '';
        $this->alamat = $pendidik->alamat ?? '';
        $this->wa = $pendidik->wa ?? '';
        $this->ibu = $pendidik->ibu ?? '';
        $this->markas_id = $pendidik->markas_id === null ? '' : (string) $pendidik->markas_id;
        $this->mapel_id = $pendidik->mapel_id === null ? '' : (string) $pendidik->mapel_id;
        $this->resetErrorBag();
    }

    public function simpan(): void
    {
        abort_unless($this->lihatId !== null, 404);

        $actor = auth()->user();
        $pendidik = $this->authorizeRow($this->lihatId);
        $markasRules = ['required', 'integer', 'exists:adm_markas,id'];

        if (! $actor->isSuperAdmin()) {
            $markasRules[] = Rule::in($actor->markasIds());
        }

        $validated = $this->validate([
            'nama' => ['required'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->lihatId)],
            'nik' => ['nullable', 'string'],
            'nip' => ['nullable', 'string'],
            'tempat_lahir' => ['nullable', 'string'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'wa' => ['nullable', 'string'],
            'ibu' => ['nullable', 'string'],
            'markas_id' => $markasRules,
            'mapel_id' => ['nullable', 'integer', 'exists:mapels,id'],
        ]);

        User::whereKey($this->lihatId)->update([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
        ]);

        $pendidik->update([
            'nik' => $validated['nik'] ?: null,
            'nip' => $validated['nip'] ?: null,
            'tempat_lahir' => $validated['tempat_lahir'] ?: null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?: null,
            'alamat' => $validated['alamat'] ?: null,
            'wa' => $validated['wa'] ?: null,
            'ibu' => $validated['ibu'] ?: null,
            'markas_id' => (int) $validated['markas_id'],
            'mapel_id' => ($validated['mapel_id'] ?? '') === '' || $validated['mapel_id'] === null
                ? null
                : (int) $validated['mapel_id'],
        ]);

        $this->halaman = 'lihat';
        $this->resetErrorBag();
    }

    public function kembali(): void
    {
        $this->halaman = 'daftar';
        $this->lihatId = null;
        $this->reset([
            'nama',
            'email',
            'nik',
            'nip',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'wa',
            'ibu',
            'mapel_id',
        ]);
        $this->resetErrorBag();
        $this->prefillMarkas();
    }

    public function render()
    {
        $actor = auth()->user();
        $pendidiks = AdminVisibility::pendidikQuery($actor)
            ->with(['pendidik.markas', 'pendidik.mapel'])
            ->when($this->cari, fn ($query) => $query->where(function ($query) {
                $search = '%'.$this->cari.'%';

                $query->where('users.nama', 'like', $search)
                    ->orWhere('users.email', 'like', $search);
            }))
            ->orderBy('users.nama')
            ->paginate(10);

        $pendidikAktif = null;

        if ($this->lihatId !== null) {
            $pendidikAktif = $this->authorizeRow($this->lihatId);
            $pendidikAktif->load(['user', 'markas', 'mapel']);
        }

        $markasList = $actor->isSuperAdmin()
            ? Markas::orderBy('markas')->get()
            : Markas::whereIn('id', $actor->markasIds())->orderBy('markas')->get();

        return view('livewire.admin.master-pendidik', [
            'pendidiks' => $pendidiks,
            'pendidikAktif' => $pendidikAktif,
            'markasList' => $markasList,
            'mapelList' => Mapel::query()->orderBy('mapel')->get(),
        ]);
    }

    private function authorizeRow(int $userId): Pendidik
    {
        $actor = auth()->user();
        abort_unless(User::where('role_id', 3)->whereKey($userId)->exists(), 404);
        $pendidik = Pendidik::where('pendidik_id', $userId)->firstOrFail();

        if (! $actor->isSuperAdmin()) {
            abort_unless(
                $pendidik->markas_id !== null
                && in_array((int) $pendidik->markas_id, $actor->markasIds(), true),
                403
            );
        }

        return $pendidik;
    }

    private function prefillMarkas(): void
    {
        $actor = auth()->user();

        if (! $actor->isSuperAdmin()) {
            $markasIds = $actor->markasIds();
            $this->markas_id = count($markasIds) === 1 ? (string) $markasIds[0] : '';

            return;
        }

        $this->markas_id = '';
    }
}
