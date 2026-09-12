<?php

namespace App\Livewire\Admin;

use App\Markas;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Markas')]
class MasterMarkas extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $cari = '';

    public string $nama = '';

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
        $markas = Markas::findOrFail($id);
        $this->editId = $markas->id;
        $this->nama = $markas->markas;
        $this->resetErrorBag();
    }

    public function batal(): void
    {
        $this->editId = null;
        $this->nama = '';
        $this->resetErrorBag();
    }

    public function simpan(): void
    {
        $validated = $this->validate([
            'nama' => [
                'required',
                Rule::unique('adm_markas', 'markas')->ignore($this->editId),
            ],
        ]);

        if ($this->editId === null) {
            Markas::create(['markas' => $validated['nama']]);
            $this->resetPage();
        } else {
            Markas::findOrFail($this->editId)->update(['markas' => $validated['nama']]);
        }

        $this->batal();
    }

    public function hapus(int $id): void
    {
        $markas = Markas::findOrFail($id);

        if (
            $markas->kelas()->exists()
            || $markas->pelajar()->exists()
            || $markas->pendidik()->exists()
            || $markas->admins()->exists()
        ) {
            $this->addError('hapus', 'Markas masih dipakai');

            return;
        }

        $markas->delete();

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function render()
    {
        $markasList = Markas::query()
            ->when($this->cari, fn ($query) => $query->where('markas', 'like', '%'.$this->cari.'%'))
            ->orderBy('markas')
            ->paginate(10);

        return view('livewire.admin.master-markas', [
            'markasList' => $markasList,
        ]);
    }
}
