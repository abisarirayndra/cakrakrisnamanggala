<?php

namespace App\Livewire\Admin;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Support\AdminVisibility;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-cakra')]
#[Title('Absensi')]
class AbsensiSlot extends Component
{
    public string $kelas_id = '';
    public string $jadwal_id = '';
    public string $mode = 'datang';
    public string $token = '';
    public string $jurnal = '';
    public string $pesan = '';
    public string $izin_user_id = '';
    public string $izin_status = '2';
    public string $izin_keterangan = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function updatedKelasId(): void
    {
        $this->jadwal_id = '';
        $this->reset(['token', 'jurnal', 'pesan', 'izin_user_id', 'izin_keterangan']);
        $this->izin_status = '2';
        $this->mode = 'datang';
        $this->resetErrorBag();
    }

    public function render()
    {
        $actor = auth()->user();
        $kelasAktif = $this->kelas_id === ''
            ? null
            : AdminVisibility::kelasForJadwal($actor)->whereKey($this->kelas_id)->first();

        $slots = collect();
        if ($kelasAktif) {
            $slots = AdminVisibility::jadwalQuery($actor)
                ->with(['mapel', 'pendidik'])
                ->where('adm_jadwal.kelas_id', $kelasAktif->id)
                ->whereDate('adm_jadwal.mulai', now()->toDateString())
                ->orderBy('adm_jadwal.mulai')
                ->get();
        }

        $slot = $this->jadwal_id === ''
            ? null
            : $slots->firstWhere('id', (int) $this->jadwal_id);

        return view('livewire.admin.absensi-slot', [
            'kelasList' => AdminVisibility::kelasForJadwal($actor)->with('markas')->get(),
            'slots' => $slots,
            'slot' => $slot,
            'hadirPendidik' => $slot ? AbsensiPendidik::query()->where('jadwal_id', $slot->id)->with('pendidik')->get() : collect(),
            'hadirPelajar' => $slot ? AbsensiPelajar::query()->where('jadwal_id', $slot->id)->with('pelajar')->get() : collect(),
            'pendidikList' => $kelasAktif ? AdminVisibility::pendidikForKelas($kelasAktif)->get() : collect(),
            'pelajarList' => $kelasAktif ? AdminVisibility::pelajarForKelas($kelasAktif)->get() : collect(),
        ]);
    }
}
