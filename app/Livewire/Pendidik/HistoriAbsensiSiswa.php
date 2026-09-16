<?php

namespace App\Livewire\Pendidik;

use App\AbsensiPelajar;
use App\Jadwal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-pendidik')]
#[Title('Histori Absensi')]
class HistoriAbsensiSiswa extends Component
{
    public string $bulan = '';

    public string $tahun = '';

    public string $cari = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->isPengajar(), 403);
    }

    public function mount(): void
    {
        $this->bulan = $this->bulan !== '' ? $this->bulan : now()->format('m');
        $this->tahun = $this->tahun !== '' ? $this->tahun : now()->format('Y');
    }

    public function render()
    {
        $slots = Jadwal::query()
            ->with(['mapel', 'kelas', 'absensiPendidik' => fn ($query) => $query->where('pendidik_id', auth()->id())])
            ->where('pendidik_id', auth()->id())
            ->whereMonth('mulai', $this->bulan)
            ->whereYear('mulai', $this->tahun)
            ->orderByDesc('mulai')
            ->get();

        $hadir = AbsensiPelajar::query()
            ->with('pelajar')
            ->whereIn('jadwal_id', $slots->pluck('id'))
            ->when($this->cari !== '', function ($query) {
                $query->whereHas('pelajar', fn ($pelajar) => $pelajar->where('nama', 'like', '%'.$this->cari.'%'));
            })
            ->get()
            ->groupBy(fn ($row) => (int) $row->jadwal_id);

        return view('livewire.pendidik.histori-absensi-siswa', [
            'slots' => $slots,
            'hadir' => $hadir,
        ]);
    }
}
