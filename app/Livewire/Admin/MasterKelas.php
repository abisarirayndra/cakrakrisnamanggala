<?php

namespace App\Livewire\Admin;

use App\Kelas;
use App\Markas;
use App\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Kelas')]
class MasterKelas extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $cari = '';

    public string $nama = '';

    public $markas_id = '';

    public ?int $editId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function ubah(int $id): void
    {
        $kelas = Kelas::findOrFail($id);
        $this->editId = $kelas->id;
        $this->nama = $kelas->nama;
        $this->markas_id = $kelas->markas_id === null ? '' : (string) $kelas->markas_id;
        $this->resetErrorBag();
    }

    public function batal(): void
    {
        $this->editId = null;
        $this->nama = '';
        $this->markas_id = '';
        $this->resetErrorBag();
    }

    public function simpan(): void
    {
        $validated = $this->validate([
            'nama' => [
                'required',
                Rule::unique('kelas', 'nama')->ignore($this->editId),
            ],
            'markas_id' => ['required', 'integer', 'exists:adm_markas,id'],
        ]);

        $payload = [
            'nama' => $validated['nama'],
            'markas_id' => (int) $validated['markas_id'],
        ];

        if ($this->editId === null) {
            Kelas::create($payload);
            $this->resetPage();
        } else {
            Kelas::findOrFail($this->editId)->update($payload);
        }

        $this->batal();
    }

    public function hapus(int $id): void
    {
        $kelas = Kelas::findOrFail($id);

        if (User::where('kelas_id', $id)->exists()) {
            $this->addError('hapus', 'Kelas masih dipakai');

            return;
        }

        $kelas->delete();

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function render()
    {
        $kelasList = Kelas::query()
            ->with('markas')
            ->when($this->cari, fn ($query) => $query->where('nama', 'like', '%'.$this->cari.'%'))
            ->orderBy('nama')
            ->paginate(10);

        return view('livewire.admin.master-kelas', [
            'kelasList' => $kelasList,
            'markasOptions' => Markas::query()->orderBy('markas')->get(),
        ]);
    }
}
