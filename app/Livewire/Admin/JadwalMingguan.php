<?php

namespace App\Livewire\Admin;

use App\Jadwal;
use App\Mapel;
use App\Support\AdminVisibility;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-cakra')]
#[Title('Jadwal')]
class JadwalMingguan extends Component
{
    public string $kelas_id = '';
    public string $senin = '';
    public string $hari = '0';
    public string $mapel_id = '';
    public string $pendidik_id = '';
    public string $jam_mulai = '';
    public string $jam_selesai = '';
    public ?int $editId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $this->senin = now()->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    public function updatedSenin(): void
    {
        if ($this->senin === '') {
            return;
        }
        $this->senin = Carbon::parse($this->senin)->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->batal();
    }

    public function updatedKelasId(): void
    {
        $this->batal();
    }

    public function batal(): void
    {
        $this->editId = null;
        $this->reset(['hari', 'mapel_id', 'pendidik_id', 'jam_mulai', 'jam_selesai']);
        $this->hari = '0';
        $this->resetErrorBag();
    }

    public function render()
    {
        $actor = auth()->user();
        $start = Carbon::parse($this->senin)->startOfDay();
        $end = $start->copy()->addDays(6)->endOfDay();

        $slots = collect();
        if ($this->kelas_id !== '') {
            $slots = AdminVisibility::jadwalQuery($actor)
                ->with(['mapel', 'pendidik', 'kelas'])
                ->where('adm_jadwal.kelas_id', $this->kelas_id)
                ->whereBetween('adm_jadwal.mulai', [$start, $end])
                ->orderBy('adm_jadwal.mulai')
                ->get();
        }

        $kelasAktif = $this->kelas_id === ''
            ? null
            : AdminVisibility::kelasForJadwal($actor)->whereKey($this->kelas_id)->first();

        return view('livewire.admin.jadwal-mingguan', [
            'kelasList' => AdminVisibility::kelasForJadwal($actor)->with('markas')->get(),
            'mapelList' => Mapel::orderBy('mapel')->get(),
            'pendidikList' => $kelasAktif
                ? AdminVisibility::pendidikForKelas($kelasAktif)->get()
                : collect(),
            'hariList' => [
                0 => 'Senin',
                1 => 'Selasa',
                2 => 'Rabu',
                3 => 'Kamis',
                4 => 'Jumat',
                5 => 'Sabtu',
                6 => 'Minggu',
            ],
            'slotsByDay' => $slots->groupBy(fn (Jadwal $row) => $row->mulai->toDateString()),
            'seninCarbon' => $start,
        ]);
    }
}
