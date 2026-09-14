<?php

namespace App\Livewire\Admin;

use App\Kelas;
use App\Markas;
use App\Pelajar;
use App\Support\AdminVisibility;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Pelajar')]
class MasterPelajar extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $cari = '';

    public string $halaman = 'daftar';

    public string $tab = 'aktif';

    public ?int $pelajarUserId = null;

    public $nik = null;

    public $nisn = null;

    public $tempat_lahir = null;

    public $tanggal_lahir = null;

    public $alamat = null;

    public $sekolah = null;

    public $wa = null;

    public $ibu = null;

    public $wali = null;

    public $wa_wali = null;

    public $markas_id = null;

    public $kelas_id = null;

    public function boot(): void
    {
        $actor = auth()->user();

        abort_unless($actor && $actor->isAdmin(), 403);
    }

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function pilihTab(string $tab): void
    {
        abort_unless(in_array($tab, ['aktif', 'suspended'], true), 404);
        $this->tab = $tab;
        $this->resetPage();
    }

    public function lihat(int $userId): void
    {
        $this->authorizeRow($userId);
        $this->pelajarUserId = $userId;
        $this->halaman = 'lihat';
    }

    public function edit(int $userId): void
    {
        $pelajar = $this->authorizeRow($userId);

        $this->pelajarUserId = $userId;
        $this->halaman = 'edit';
        $this->nik = $pelajar->nik;
        $this->nisn = $pelajar->nisn;
        $this->tempat_lahir = $pelajar->tempat_lahir;
        $this->tanggal_lahir = optional($pelajar->tanggal_lahir)->format('Y-m-d');
        $this->alamat = $pelajar->alamat;
        $this->sekolah = $pelajar->sekolah;
        $this->wa = $pelajar->wa;
        $this->ibu = $pelajar->ibu;
        $this->wali = $pelajar->wali;
        $this->wa_wali = $pelajar->wa_wali;
        $this->markas_id = $pelajar->markas_id === null ? null : (string) $pelajar->markas_id;
        $this->kelas_id = $pelajar->user->kelas_id === null ? null : (string) $pelajar->user->kelas_id;
        $this->resetErrorBag();
    }

    public function updatedMarkasId($value): void
    {
        if ($this->kelas_id === null || $this->kelas_id === '') {
            return;
        }

        $masihCocok = Kelas::query()
            ->whereKey($this->kelas_id)
            ->where('markas_id', $value)
            ->exists();

        if (! $masihCocok) {
            $this->kelas_id = null;
        }
    }

    public function simpan(): void
    {
        abort_unless($this->pelajarUserId !== null, 404);

        $actor = auth()->user();
        $pelajar = $this->authorizeRow($this->pelajarUserId);

        if ($this->kelas_id === '') {
            $this->kelas_id = null;
        }

        $markasRules = ['required', 'integer', 'exists:adm_markas,id'];

        if (! $actor->isSuperAdmin()) {
            $markasRules[] = Rule::in($actor->markasIds());
        }

        $kelasRules = [
            'nullable',
            'integer',
            Rule::exists('kelas', 'id')->where(fn ($query) => $query->where('markas_id', $this->markas_id)),
        ];

        if (! $actor->isSuperAdmin()) {
            $kelasRules[] = Rule::in($this->kelasIdsForActor($actor));
        }

        $validated = $this->validate([
            'nik' => ['nullable', 'string'],
            'nisn' => ['nullable', 'string'],
            'tempat_lahir' => ['nullable', 'string'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'sekolah' => ['nullable', 'string'],
            'wa' => ['nullable', 'string'],
            'ibu' => ['nullable', 'string'],
            'wali' => ['nullable', 'string'],
            'wa_wali' => ['nullable', 'string'],
            'markas_id' => $markasRules,
            'kelas_id' => $kelasRules,
        ]);

        $kelasId = $validated['kelas_id'] ?? null;
        unset($validated['kelas_id']);

        $pelajar->update($validated);
        User::whereKey($this->pelajarUserId)->update(['kelas_id' => $kelasId]);
        $this->halaman = 'lihat';
        $this->resetErrorBag();
    }

    public function suspend(int $userId): void
    {
        $this->authorizeRow($userId);
        User::where('role_id', 4)->findOrFail($userId)->update(['role_id' => 6]);
        $this->kembali();
    }

    public function unsuspend(int $userId): void
    {
        $this->authorizeRow($userId);
        User::where('role_id', 6)->findOrFail($userId)->update(['role_id' => 4]);
        $this->kembali();
    }

    public function hapus(int $userId): void
    {
        $pelajar = $this->authorizeRow($userId);
        $user = User::findOrFail($userId);

        if ($pelajar->foto) {
            File::delete(public_path('img/pelajar/'.$pelajar->foto));
        }

        $user->delete();
        $this->kembali();
    }

    public function kembali(): void
    {
        $this->halaman = 'daftar';
        $this->pelajarUserId = null;
        $this->reset([
            'nik',
            'nisn',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'sekolah',
            'wa',
            'ibu',
            'wali',
            'wa_wali',
            'markas_id',
            'kelas_id',
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        $actor = auth()->user();
        $roleId = $this->tab === 'suspended' ? 6 : 4;
        $pelajars = AdminVisibility::pelajarQuery($actor, $roleId)
            ->with(['pelajar.markas', 'kelas'])
            ->when($this->cari, fn ($query) => $query->where(function ($query) {
                $search = '%'.$this->cari.'%';

                $query->where('users.nama', 'like', $search)
                    ->orWhere('users.email', 'like', $search)
                    ->orWhere('users.nomor_registrasi', 'like', $search);
            }))
            ->orderBy('users.nama')
            ->paginate(10);

        $pelajarAktif = null;

        if ($this->pelajarUserId !== null) {
            $pelajarAktif = $this->authorizeRow($this->pelajarUserId);
            $pelajarAktif->load(['user.kelas', 'markas']);
        }

        $markasList = $actor->isSuperAdmin()
            ? Markas::orderBy('markas')->get()
            : Markas::whereIn('id', $actor->markasIds())->orderBy('markas')->get();

        return view('livewire.admin.master-pelajar', [
            'pelajars' => $pelajars,
            'pelajarAktif' => $pelajarAktif,
            'markasList' => $markasList,
            'kelasList' => $this->kelasListFor($actor),
        ]);
    }

    private function authorizeRow(int $userId): Pelajar
    {
        $actor = auth()->user();
        abort_unless(User::whereIn('role_id', [4, 6])->whereKey($userId)->exists(), 404);
        $pelajar = Pelajar::where('pelajar_id', $userId)->firstOrFail();

        if (! $actor->isSuperAdmin()) {
            abort_unless(
                $pelajar->markas_id !== null
                && in_array((int) $pelajar->markas_id, $actor->markasIds(), true),
                403
            );
        }

        return $pelajar;
    }

    private function kelasListFor(User $actor)
    {
        $query = Kelas::query()->orderBy('nama');

        if ($this->markas_id) {
            $query->where('markas_id', $this->markas_id);
        } elseif (! $actor->isSuperAdmin()) {
            $ids = $actor->markasIds();
            $ids === [] ? $query->whereRaw('1 = 0') : $query->whereIn('markas_id', $ids);
        }

        return $query->get();
    }

    private function kelasIdsForActor(User $actor): array
    {
        $ids = $actor->markasIds();

        if ($ids === []) {
            return [];
        }

        return Kelas::query()
            ->whereIn('markas_id', $ids)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
