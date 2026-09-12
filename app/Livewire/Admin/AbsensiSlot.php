<?php

namespace App\Livewire\Admin;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Support\AbsensiStatus;
use App\Support\AdminVisibility;
use App\User;
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

    public function scan(): void
    {
        $this->pesan = '';
        $this->validate(['token' => 'required']);

        $slot = $this->slotAktif();

        if (! $this->dalamJendelaAbsensi($slot)) {
            $this->addError('token', 'Di luar jam absensi');

            return;
        }

        $user = User::query()->where('nomor_registrasi', trim($this->token))->first();

        if (! $user) {
            $this->addError('token', 'Nomor registrasi tidak ditemukan');

            return;
        }

        $isPelajar = (int) $user->role_id === 4;
        $isPendidik = (int) $user->role_id === 3;

        if ($isPelajar && (int) $user->kelas_id !== (int) $slot->kelas_id) {
            $this->addError('token', 'Bukan pelajar kelas ini');

            return;
        }

        if ($isPendidik && ! AdminVisibility::pendidikForKelas($slot->kelas)->whereKey($user->id)->exists()) {
            $this->addError('token', 'Bukan pendidik markas ini');

            return;
        }

        if (! $isPelajar && ! $isPendidik) {
            $this->addError('token', 'Kartu tidak untuk absensi mapel');

            return;
        }

        if ($this->mode !== 'datang') {
            return;
        }

        if ($isPelajar) {
            $existing = AbsensiPelajar::query()
                ->where('jadwal_id', $slot->id)
                ->where('pelajar_id', $user->id)
                ->first();

            if ($existing?->datang !== null) {
                $this->addError('token', 'Sudah absen datang');

                return;
            }

            AbsensiPelajar::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pelajar_id' => $user->id],
                ['datang' => now(), 'status' => AbsensiStatus::HADIR]
            );
        } else {
            $existing = AbsensiPendidik::query()
                ->where('jadwal_id', $slot->id)
                ->where('pendidik_id', $user->id)
                ->first();

            if ($existing?->datang !== null) {
                $this->addError('token', 'Sudah absen datang');

                return;
            }

            AbsensiPendidik::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pendidik_id' => $user->id],
                ['datang' => now(), 'status' => AbsensiStatus::HADIR]
            );
        }

        $this->token = '';
        $this->pesan = $user->nama.' — Datang';
    }

    public function slotAktif(): Jadwal
    {
        return AdminVisibility::jadwalQuery(auth()->user())
            ->whereKey($this->jadwal_id)
            ->firstOrFail();
    }

    protected function dalamJendelaAbsensi(Jadwal $slot): bool
    {
        return now()->between($slot->mulai->copy()->subHour(), $slot->selesai->copy()->addHour(), true);
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
