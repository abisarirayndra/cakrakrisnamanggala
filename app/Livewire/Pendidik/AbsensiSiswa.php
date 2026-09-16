<?php

namespace App\Livewire\Pendidik;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Support\AbsensiStatus;
use App\Support\AdminVisibility;
use App\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-pendidik')]
#[Title('Absensi Siswa')]
class AbsensiSiswa extends Component
{
    public string $jadwal_id = '';

    public string $cari = '';

    public string $jurnal = '';

    public string $izin_user_id = '';

    public string $izin_status = '2';

    public string $izin_keterangan = '';

    public bool $lewatiAlpa = false;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isPengajar(), 403);
    }

    public function updatedJadwalId(): void
    {
        $this->muatJurnal();
        $this->reset(['izin_user_id', 'izin_keterangan', 'lewatiAlpa']);
        $this->izin_status = '2';
        $this->resetErrorBag();
    }

    public function simpanJurnal(): void
    {
        $slot = $this->slotMilik();
        abort_unless($slot !== null, 403);

        $this->validate([
            'jurnal' => ['required', 'string'],
        ]);

        $existing = AbsensiPendidik::query()
            ->where('jadwal_id', $slot->id)
            ->where('pendidik_id', auth()->id())
            ->first();

        $payload = ['jurnal' => $this->jurnal];

        if ($existing?->datang === null) {
            $payload['datang'] = $slot->mulai;
            $payload['pulang'] = $slot->selesai;
            $payload['status'] = AbsensiStatus::HADIR;
        }

        AbsensiPendidik::updateOrCreate(
            ['jadwal_id' => $slot->id, 'pendidik_id' => auth()->id()],
            $payload
        );
    }

    public function simpanIzin(): void
    {
        $slot = $this->slotMilik();
        abort_unless($slot !== null, 403);

        $allowedIds = AdminVisibility::pelajarForKelas($slot->kelas)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $this->validate([
            'izin_user_id' => 'required|in:'.implode(',', $allowedIds),
            'izin_status' => 'required|in:2,3,4',
        ]);

        if ((int) $this->izin_status === AbsensiStatus::IZIN && trim($this->izin_keterangan) === '') {
            $this->addError('izin_keterangan', 'Keterangan wajib untuk izin');

            return;
        }

        $user = User::query()->findOrFail($this->izin_user_id);
        $existing = AbsensiPelajar::query()
            ->where('jadwal_id', $slot->id)
            ->where('pelajar_id', $user->id)
            ->first();

        if ($existing && $existing->datang !== null) {
            $this->addError('izin_user_id', 'Sudah hadir');

            return;
        }

        AbsensiPelajar::updateOrCreate(
            ['jadwal_id' => $slot->id, 'pelajar_id' => $user->id],
            [
                'status' => (int) $this->izin_status,
                'keterangan' => trim($this->izin_keterangan) === '' ? null : $this->izin_keterangan,
                'datang' => null,
                'pulang' => null,
            ]
        );

        $this->reset(['izin_user_id', 'izin_keterangan']);
        $this->izin_status = '2';
    }

    public function toggle(int $pelajarId): void
    {
        $slot = $this->slotMilik();
        abort_unless($slot !== null, 403);

        $allowed = AdminVisibility::pelajarForKelas($slot->kelas)->whereKey($pelajarId)->exists();
        abort_unless($allowed, 403);

        $existing = AbsensiPelajar::query()
            ->where('jadwal_id', $slot->id)
            ->where('pelajar_id', $pelajarId)
            ->first();

        if ($existing?->datang !== null) {
            $existing->delete();

            return;
        }

        AbsensiPelajar::updateOrCreate(
            ['jadwal_id' => $slot->id, 'pelajar_id' => $pelajarId],
            [
                'datang' => $slot->mulai,
                'pulang' => $slot->selesai,
                'status' => AbsensiStatus::HADIR,
                'keterangan' => null,
            ]
        );
    }

    public function tandaiSisaAlpa(): void
    {
        $slot = $this->slotMilik();
        abort_unless($slot !== null, 403);

        foreach ($this->pelajarSisaAlpa($slot) as $siswa) {
            AbsensiPelajar::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pelajar_id' => $siswa->id],
                [
                    'status' => AbsensiStatus::ALPA,
                    'datang' => null,
                    'pulang' => null,
                    'keterangan' => null,
                ]
            );
        }

        $this->lewatiAlpa = false;
    }

    public function lewatiSisaAlpa(): void
    {
        abort_unless($this->slotMilik() !== null, 403);
        $this->lewatiAlpa = true;
    }

    public function render()
    {
        $slots = $this->slotsHariIni();

        if ($this->jadwal_id === '' && $slots->isNotEmpty()) {
            $this->jadwal_id = (string) $slots->first()->id;
            $this->muatJurnal();
        }

        $slot = $this->slotMilik();
        $pelajarKelas = $slot
            ? AdminVisibility::pelajarForKelas($slot->kelas)->get()
            : collect();
        $pelajarList = $slot
            ? AdminVisibility::pelajarForKelas($slot->kelas)
                ->when($this->cari !== '', fn ($query) => $query->where('users.nama', 'like', '%'.$this->cari.'%'))
                ->get()
            : collect();
        $absensiMap = $slot
            ? AbsensiPelajar::query()
                ->where('jadwal_id', $slot->id)
                ->get()
                ->keyBy(fn ($row) => (int) $row->pelajar_id)
            : collect();

        return view('livewire.pendidik.absensi-siswa', [
            'slots' => $slots,
            'slot' => $slot,
            'pelajarKelas' => $pelajarKelas,
            'pelajarList' => $pelajarList,
            'absensiMap' => $absensiMap,
            'adaSisaAlpa' => $slot ? $this->pelajarSisaAlpa($slot, $pelajarKelas, $absensiMap)->isNotEmpty() : false,
        ]);
    }

    private function slotsHariIni()
    {
        return Jadwal::query()
            ->with(['mapel', 'kelas'])
            ->where('pendidik_id', auth()->id())
            ->whereDate('mulai', now()->toDateString())
            ->orderBy('mulai')
            ->get();
    }

    private function slotMilik(): ?Jadwal
    {
        if ($this->jadwal_id === '') {
            return null;
        }

        return $this->slotsHariIni()->firstWhere('id', (int) $this->jadwal_id);
    }

    private function pelajarSisaAlpa($slot, $pelajarKelas = null, $absensiMap = null)
    {
        $pelajarKelas = $pelajarKelas ?? AdminVisibility::pelajarForKelas($slot->kelas)->get();
        $absensiMap = $absensiMap ?? AbsensiPelajar::query()
            ->where('jadwal_id', $slot->id)
            ->get()
            ->keyBy(fn ($row) => (int) $row->pelajar_id);

        return $pelajarKelas->filter(function ($siswa) use ($absensiMap) {
            $absensi = $absensiMap->get((int) $siswa->id);

            if ($absensi === null) {
                return true;
            }

            if ($absensi->datang !== null) {
                return false;
            }

            return ! in_array((int) $absensi->status, [
                AbsensiStatus::IZIN,
                AbsensiStatus::SAKIT,
                AbsensiStatus::ALPA,
            ], true);
        });
    }

    private function muatJurnal(): void
    {
        $slot = $this->slotMilik();

        $this->jurnal = $slot
            ? (string) (AbsensiPendidik::query()
                ->where('jadwal_id', $slot->id)
                ->where('pendidik_id', auth()->id())
                ->value('jurnal') ?? '')
            : '';
    }
}
