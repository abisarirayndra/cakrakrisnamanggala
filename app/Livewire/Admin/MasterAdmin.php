<?php

namespace App\Livewire\Admin;

use App\Markas;
use App\User;
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

    public string $cari = '';

    public string $nama = '';

    public string $email = '';

    public string $password = '';

    public bool $is_super_admin = false;

    public $markas_id = '';

    public ?int $lihatId = null;

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
            ],
        ], [
            'markas_id.required' => 'Markas wajib untuk admin non-super',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 2,
            'is_super_admin' => $validated['is_super_admin'],
        ]);

        if ($this->markas_id !== '' && $this->markas_id !== null) {
            $user->markas()->attach((int) $this->markas_id);
        }

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

        User::where('role_id', 2)->findOrFail($id)->delete();

        if ($this->lihatId === $id) {
            $this->kembali();
        }
    }

    public function lihat(int $id): void
    {
        User::where('role_id', 2)->findOrFail($id);
        $this->lihatId = $id;
    }

    public function kembali(): void
    {
        $this->lihatId = null;
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
            ->orderByDesc('id')
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
