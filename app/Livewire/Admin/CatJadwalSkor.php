<?php

namespace App\Livewire\Admin;

use App\CatJadwal as Jadwal;
use App\Support\AdminVisibility;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-cakra')]
#[Title('Live skor CAT')]
class CatJadwalSkor extends Component
{
    public int $jadwalId;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(int|string|Jadwal $jadwal): void
    {
        $this->jadwalId = $jadwal instanceof Jadwal ? (int) $jadwal->id : (int) $jadwal;
        abort_unless($this->queryJadwal()->exists(), 403);
    }

    public function render()
    {
        $jadwal = $this->queryJadwal()
            ->with(['banks.soal.opsi', 'sesi.pelajar.kelas', 'sesi.jawaban'])
            ->firstOrFail();

        return view('livewire.admin.cat-jadwal-skor', [
            'jadwal' => $jadwal,
            'baris' => $jadwal->ringkasanPelajar(),
        ]);
    }

    private function queryJadwal()
    {
        return AdminVisibility::catJadwalQuery(auth()->user())->whereKey($this->jadwalId);
    }
}
