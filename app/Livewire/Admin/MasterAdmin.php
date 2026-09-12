<?php

namespace App\Livewire\Admin;

use App\Markas;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Admin')]
class MasterAdmin extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $cari = '';

    public string $halaman = 'daftar';

    public string $nama = '';

    public string $email = '';

    public string $password = '';

    public bool $is_super_admin = false;

    public $markas_id = '';

    public ?int $lihatId = null;

    public ?int $editId = null;

    public function boot(): void
    {
        $user = auth()->user();

        abort_unless($user && $user->isSuperAdmin(), 403);
    }

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function tambah(): void
    {
        $validated = $this->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'is_super_admin' => 'boolean',
            'markas_id' => [
                Rule::requiredIf(! $this->is_super_admin),
                'nullable',
                'integer',
                'exists:adm_markas,id',
            ],
        ], [
            'markas_id.required' => 'Markas wajib untuk admin non-super',
        ]);

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => 2,
                'is_super_admin' => $validated['is_super_admin'],
            ]);

            $markasId = $validated['markas_id'] ?? null;

            if ($markasId !== '' && $markasId !== null) {
                $user->markas()->attach((int) $markasId);
            }
        });

        $this->reset(['nama', 'email', 'password', 'is_super_admin', 'markas_id']);
        $this->resetErrorBag();
        $this->resetPage();
    }

    public function hapus(int $id): void
    {
        if ($id === (int) auth()->id()) {
            $this->addError('hapus', 'Tidak bisa menghapus akun sendiri');

            return;
        }

        $user = User::where('role_id', 2)->findOrFail($id);
        $user->markas()->detach();
        $user->delete();

        if ($this->lihatId === $id) {
            $this->kembali();
        }

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function lihat(int $id): void
    {
        User::where('role_id', 2)->findOrFail($id);
        $this->lihatId = $id;
        $this->halaman = 'lihat';
    }

    public function edit(int $id): void
    {
        $user = User::where('role_id', 2)->with('markas')->findOrFail($id);
        $ids = $user->markasIds();

        $this->halaman = 'daftar';
        $this->lihatId = null;
        $this->editId = $id;
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->password = '';
        $this->is_super_admin = $user->isSuperAdmin();
        $this->markas_id = $ids === [] ? '' : (string) $ids[0];
        $this->resetErrorBag();
    }

    public function batal(): void
    {
        $this->editId = null;
        $this->reset(['nama', 'email', 'password', 'is_super_admin', 'markas_id']);
        $this->resetErrorBag();
    }

    public function simpan(): void
    {
        abort_unless($this->editId !== null, 404);

        $user = User::where('role_id', 2)->findOrFail($this->editId);

        if ($this->editId === (int) auth()->id() && $user->isSuperAdmin() && ! $this->is_super_admin) {
            $this->addError('is_super_admin', 'Tidak bisa mencabut superadmin akun sendiri');

            return;
        }

        $validated = $this->validate([
            'nama' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editId)],
            'password' => 'nullable|min:6',
            'is_super_admin' => 'boolean',
            'markas_id' => [
                Rule::requiredIf(! $this->is_super_admin),
                'nullable',
                'integer',
                'exists:adm_markas,id',
            ],
        ], [
            'markas_id.required' => 'Markas wajib untuk admin non-super',
        ]);

        $payload = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'is_super_admin' => $validated['is_super_admin'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        $markasId = $validated['markas_id'] ?? null;

        if ($markasId !== '' && $markasId !== null) {
            $user->markas()->sync([(int) $markasId]);
        } else {
            $user->markas()->sync([]);
        }

        $this->batal();
    }

    public function kembali(): void
    {
        $this->halaman = 'daftar';
        $this->lihatId = null;
        $this->batal();
    }

    public function render()
    {
        $admins = User::query()
            ->where('role_id', 2)
            ->with('markas')
            ->when($this->cari, fn ($q) => $q->where(function ($q) {
                $q->where('nama', 'like', '%'.$this->cari.'%')
                    ->orWhere('email', 'like', '%'.$this->cari.'%');
            }))
            ->orderBy('nama')
            ->paginate(10);

        $adminDilihat = $this->lihatId === null
            ? null
            : User::where('role_id', 2)->with('markas')->findOrFail($this->lihatId);

        return view('livewire.admin.master-admin', [
            'admins' => $admins,
            'markasList' => Markas::orderBy('markas')->get(),
            'adminDilihat' => $adminDilihat,
        ]);
    }
}
