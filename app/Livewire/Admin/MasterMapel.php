<?php

namespace App\Livewire\Admin;

use App\Mapel;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.panel-cakra')]
#[Title('Mapel')]
class MasterMapel extends Component
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
        $mapel = Mapel::findOrFail($id);
        $this->editId = $mapel->id;
        $this->nama = $mapel->mapel;
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
                Rule::unique('mapels', 'mapel')->ignore($this->editId),
            ],
        ]);

        if ($this->editId === null) {
            Mapel::create(['mapel' => $validated['nama']]);
            $this->resetPage();
        } else {
            Mapel::findOrFail($this->editId)->update(['mapel' => $validated['nama']]);
        }

        $this->batal();
    }

    public function hapus(int $id): void
    {
        $mapel = Mapel::findOrFail($id);

        if (
            $mapel->pendidik()->exists()
            || $mapel->tes()->exists()
            || $mapel->tema()->exists()
            || $mapel->jadwal()->exists()
        ) {
            $this->addError('hapus', 'Mapel masih dipakai');

            return;
        }

        $mapel->delete();

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function render()
    {
        $mapelList = Mapel::query()
            ->when($this->cari, fn ($query) => $query->where('mapel', 'like', '%'.$this->cari.'%'))
            ->orderBy('mapel')
            ->paginate(10);

        return view('livewire.admin.master-mapel', [
            'mapelList' => $mapelList,
        ]);
    }
}
