<?php

namespace App\Livewire\Admin;

use App\Jadwal;
use App\Support\AdminVisibility;
use App\Support\RingkasanJadwal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-cakra')]
#[Title('Histori Jadwal')]
class HistoriJadwal extends Component
{
    public string $kelas_id = '';

    public string $bulan = '';

    public string $tahun = '';

    public ?int $detailId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $this->bulan = $this->bulan !== '' ? $this->bulan : now()->format('m');
        $this->tahun = $this->tahun !== '' ? $this->tahun : now()->format('Y');
    }

    public function updatedKelasId(): void
    {
        $this->detailId = null;
    }

    public function updatedBulan(): void
    {
        $this->detailId = null;
    }

    public function updatedTahun(): void
    {
        $this->detailId = null;
    }

    public function bukaDetail(int $id): void
    {
        $this->slot($id);
        $this->detailId = $id;
    }

    public function kembali(): void
    {
        $this->detailId = null;
    }

    public function render()
    {
        $actor = auth()->user();
        $kelasAktif = $this->kelas_id === ''
            ? null
            : AdminVisibility::kelasForJadwal($actor)->whereKey($this->kelas_id)->first();

        $slots = AdminVisibility::jadwalQuery($actor)
            ->with(['mapel', 'pendidik', 'kelas.markas'])
            ->when($kelasAktif, fn ($query) => $query->where('adm_jadwal.kelas_id', $kelasAktif->id))
            ->whereMonth('adm_jadwal.mulai', $this->bulan)
            ->whereYear('adm_jadwal.mulai', $this->tahun)
            ->orderByDesc('adm_jadwal.mulai')
            ->get();

        $slot = $this->detailId === null ? null : $this->slot($this->detailId);
        $ringkasan = $slot ? RingkasanJadwal::buat($slot) : null;

        return view('livewire.admin.histori-jadwal', [
            'kelasList' => AdminVisibility::kelasForJadwal($actor)->with('markas')->get(),
            'slots' => $slots,
            'slot' => $slot,
            'datangPendidik' => $ringkasan['datangPendidik'] ?? collect(),
            'datangPelajar' => $ringkasan['datangPelajar'] ?? collect(),
            'izinPendidik' => $ringkasan['izinPendidik'] ?? collect(),
            'izinPelajar' => $ringkasan['izinPelajar'] ?? collect(),
            'jurnal' => $ringkasan['jurnal'] ?? null,
        ]);
    }

    private function slot(int $id): Jadwal
    {
        return AdminVisibility::jadwalQuery(auth()->user())
            ->with(['mapel', 'pendidik', 'kelas.markas'])
            ->where('adm_jadwal.id', $id)
            ->firstOrFail();
    }
}
