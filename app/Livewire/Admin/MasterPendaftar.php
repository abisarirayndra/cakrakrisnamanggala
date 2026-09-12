<?php

namespace App\Livewire\Admin;

use App\Kelas;
use App\Pelajar;
use App\Support\AdminVisibility;
use App\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Pendaftar')]
class MasterPendaftar extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $cari = '';

    public string $halaman = 'daftar';

    public ?int $pendaftarUserId = null;

    public string $kelas_id = '';

    public bool $showMigrasi = false;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function lihat(int $userId): void
    {
        $this->authorizeRow($userId);
        $this->pendaftarUserId = $userId;
        $this->halaman = 'lihat';
        $this->showMigrasi = false;
        $this->kelas_id = '';
        $this->resetErrorBag();
    }

    public function kembali(): void
    {
        $this->halaman = 'daftar';
        $this->pendaftarUserId = null;
        $this->showMigrasi = false;
        $this->kelas_id = '';
        $this->resetErrorBag();
    }

    public function bukaMigrasi(): void
    {
        abort_unless($this->pendaftarUserId !== null, 404);
        $this->authorizeRow($this->pendaftarUserId);
        $this->showMigrasi = true;
        $this->resetErrorBag('kelas_id');
    }

    public function tutupMigrasi(): void
    {
        $this->showMigrasi = false;
        $this->kelas_id = '';
        $this->resetErrorBag('kelas_id');
    }

    public function migrasi()
    {
        abort_unless($this->pendaftarUserId !== null, 404);

        $actor = auth()->user();
        $this->authorizeRow($this->pendaftarUserId);

        $kelasRules = ['required', 'integer', 'exists:kelas,id'];

        if (! $actor->isSuperAdmin()) {
            $kelasRules[] = Rule::in($this->kelasIdsForActor($actor));
        }

        $validated = $this->validate([
            'kelas_id' => $kelasRules,
        ]);

        $user = User::where('role_id', 5)->whereKey($this->pendaftarUserId)->firstOrFail();
        $user->update([
            'kelas_id' => (int) $validated['kelas_id'],
            'nomor_registrasi' => Str::random(6),
            'role_id' => 4,
        ]);

        return $this->redirect(route('admin.pengguna.pelajar'), navigate: false);
    }

    public function hapus(int $userId): void
    {
        $pelajar = $this->authorizeRow($userId);

        if ($pelajar->foto) {
            Storage::disk('pelajar_foto')->delete($pelajar->foto);
        }

        $pelajar->delete();
        User::whereKey($userId)->delete();

        if ($this->pendaftarUserId === $userId) {
            $this->kembali();
        }
    }

    public function render()
    {
        $actor = auth()->user();
        $pendaftars = AdminVisibility::pendaftarQuery($actor)
            ->with(['pelajar.markas'])
            ->when($this->cari, fn ($query) => $query->where(function ($query) {
                $search = '%'.$this->cari.'%';

                $query->where('users.nama', 'like', $search)
                    ->orWhere('users.email', 'like', $search);
            }))
            ->orderByDesc('users.id')
            ->paginate(10);

        $pendaftarAktif = null;

        if ($this->pendaftarUserId !== null) {
            $pendaftarAktif = $this->authorizeRow($this->pendaftarUserId);
            $pendaftarAktif->load(['user', 'markas']);
        }

        return view('livewire.admin.master-pendaftar', [
            'pendaftars' => $pendaftars,
            'pendaftarAktif' => $pendaftarAktif,
            'kelasList' => $this->kelasListFor($actor),
        ]);
    }

    private function authorizeRow(int $userId): Pelajar
    {
        $actor = auth()->user();
        abort_unless(User::where('role_id', 5)->whereKey($userId)->exists(), 404);
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

        if (! $actor->isSuperAdmin()) {
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
