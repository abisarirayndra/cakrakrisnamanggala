<?php

namespace App\Livewire\Pendidik;

use App\CatAnalisisCatatan;
use App\CatJadwal as Jadwal;
use App\Support\CatAnalisisPaket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-pendidik')]
#[Title('Analisis CAT')]
class CatAnalisis extends Component
{
    public int $jadwalId;

    public string $tab = 'soal';

    public array $catatan = [];

    public ?int $tersimpanId = null;

    public ?int $detailId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isPengajar(), 403);
    }

    public function mount(int|string|Jadwal $jadwal): void
    {
        $this->jadwalId = $jadwal instanceof Jadwal ? (int) $jadwal->id : (int) $jadwal;
        abort_unless(CatAnalisisPaket::milikPendidik(
            Jadwal::query()->findOrFail($this->jadwalId),
            (int) auth()->id()
        ), 403);
    }

    public function pilihTab(string $tab): void
    {
        if (in_array($tab, ['soal', 'siswa'], true)) {
            $this->tab = $tab;
            $this->tersimpanId = null;
            if ($tab === 'soal') {
                $this->detailId = null;
            }
        }
    }

    public function bukaDetail(int $pelajarId): void
    {
        $this->tab = 'siswa';
        $this->detailId = $pelajarId;
        $this->tersimpanId = null;
    }

    public function tutupDetail(): void
    {
        $this->detailId = null;
        $this->tersimpanId = null;
    }

    public function simpanCatatan(int $pelajarId): void
    {
        $this->validate([
            'catatan.'.$pelajarId => ['nullable', 'string', 'max:2000'],
        ]);

        $detail = $this->detail();
        abort_unless(
            collect($detail['siswa'])->contains(fn (array $item) => (int) $item['id'] === $pelajarId),
            403
        );

        CatAnalisisCatatan::query()->updateOrCreate(
            [
                'jadwal_id' => $this->jadwalId,
                'pelajar_id' => $pelajarId,
                'pendidik_id' => (int) auth()->id(),
            ],
            ['catatan' => $this->catatan[$pelajarId] ?? '']
        );

        $this->tersimpanId = $pelajarId;
    }

    public function render()
    {
        $detail = $this->detail();

        foreach ($detail['siswa'] as $item) {
            if (! array_key_exists($item['id'], $this->catatan)) {
                $this->catatan[$item['id']] = $item['catatan'];
            }
        }

        $detail['detailSiswa'] = collect($detail['siswa'])
            ->first(fn (array $item) => (int) $item['id'] === (int) $this->detailId);

        return view('livewire.pendidik.cat-analisis', $detail);
    }

    private function detail(): array
    {
        return CatAnalisisPaket::detail(
            Jadwal::query()->findOrFail($this->jadwalId),
            (int) auth()->id()
        );
    }
}
