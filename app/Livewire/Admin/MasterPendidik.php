<?php

namespace App\Livewire\Admin;

use App\Markas;
use App\Pendidik;
use App\Support\AdminVisibility;
use App\User;
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

    public string $cari = '';

    public string $nama = '';

    public string $email = '';

    public $markas_id = '';

    public ?int $lihatId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $actor = auth()->user();

        if (! $actor->isSuperAdmin()) {
            $markasIds = $actor->markasIds();

            if (count($markasIds) === 1) {
                $this->markas_id = (string) $markasIds[0];
            }
        }
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

        $this->reset(['nama', 'email']);
        $this->resetErrorBag();
        $this->resetPage();
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
    }

    public function kembali(): void
    {
        $this->lihatId = null;
    }

    public function render()
    {
        $actor = auth()->user();
        $pendidiks = AdminVisibility::pendidikQuery($actor)
            ->with('pendidik.markas')
            ->when($this->cari, fn ($query) => $query->where(function ($query) {
                $search = '%'.$this->cari.'%';

                $query->where('users.nama', 'like', $search)
                    ->orWhere('users.email', 'like', $search);
            }))
            ->orderByDesc('users.id')
            ->paginate(10);

        $pendidikDilihat = null;

        if ($this->lihatId !== null) {
            $pendidikDilihat = User::where('role_id', 3)
                ->with('pendidik.markas')
                ->findOrFail($this->lihatId);
            $this->authorizeRow($this->lihatId);
        }

        $markasList = $actor->isSuperAdmin()
            ? Markas::orderBy('markas')->get()
            : Markas::whereIn('id', $actor->markasIds())->orderBy('markas')->get();

        return view('livewire.admin.master-pendidik', [
            'pendidiks' => $pendidiks,
            'pendidikDilihat' => $pendidikDilihat,
            'markasList' => $markasList,
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
}
