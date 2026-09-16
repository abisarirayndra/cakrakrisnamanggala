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

    public bool $lewatiAlpa = false;

    public bool $showJurnalModal = false;

    public string $jurnalNama = '';

    public ?int $jurnalPendidikId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function updatedKelasId(): void
    {
        $this->jadwal_id = '';
        $this->reset(['token', 'jurnal', 'pesan', 'izin_user_id', 'izin_keterangan', 'lewatiAlpa', 'showJurnalModal', 'jurnalNama', 'jurnalPendidikId']);
        $this->izin_status = '2';
        $this->mode = 'datang';
        $this->resetErrorBag();
    }

    public function updatedJadwalId(): void
    {
        $this->reset(['token', 'jurnal', 'pesan', 'lewatiAlpa', 'showJurnalModal', 'jurnalNama', 'jurnalPendidikId']);
        $this->resetErrorBag();
        $this->fokusToken();
    }

    public function updatedMode(): void
    {
        $this->fokusToken();
    }

    public function scan(): void
    {
        try {
            $this->prosesScan();
        } finally {
            if ($this->showJurnalModal) {
                $this->js('document.getElementById("jurnal")?.focus()');
            } else {
                $this->fokusToken();
            }
        }
    }

    protected function prosesScan(): void
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

        if ($this->mode === 'pulang') {
            $this->scanPulang($slot, $user, $isPelajar);

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

            $statusDatang = AbsensiStatus::dariDatang(now(), $slot->mulai);

            AbsensiPelajar::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pelajar_id' => $user->id],
                ['datang' => now(), 'status' => $statusDatang]
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

            $statusDatang = AbsensiStatus::dariDatang(now(), $slot->mulai);

            AbsensiPendidik::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pendidik_id' => $user->id],
                ['datang' => now(), 'status' => $statusDatang]
            );
        }

        $this->token = '';
        $this->pesan = $user->nama.' — '.AbsensiStatus::label($statusDatang);
    }

    protected function scanPulang(Jadwal $slot, User $user, bool $isPelajar): void
    {
        if ($isPelajar) {
            $existing = AbsensiPelajar::query()
                ->where('jadwal_id', $slot->id)
                ->where('pelajar_id', $user->id)
                ->first();
        } else {
            $existing = AbsensiPendidik::query()
                ->where('jadwal_id', $slot->id)
                ->where('pendidik_id', $user->id)
                ->first();
        }

        if ($existing?->datang === null) {
            $this->addError('token', 'Belum absen datang');

            return;
        }

        if ($existing->pulang !== null) {
            $this->addError('token', 'Sudah absen pulang');

            return;
        }

        $guruUtama = ! $isPelajar && $user->id === (int) $slot->pendidik_id;

        if ($guruUtama) {
            $this->showJurnalModal = true;
            $this->jurnalNama = $user->nama;
            $this->jurnalPendidikId = (int) $user->id;
            $this->jurnal = '';
            $this->token = '';
            $this->resetErrorBag('jurnal');

            return;
        }

        $existing->update([
            'pulang' => now(),
        ]);

        $this->token = '';
        $this->pesan = $user->nama.' — Pulang';
    }

    public function simpanJurnalPulang(): void
    {
        $slot = $this->slotAktif();
        abort_unless($this->showJurnalModal && $this->jurnalPendidikId, 403);
        abort_unless($this->jurnalPendidikId === (int) $slot->pendidik_id, 403);

        if (trim($this->jurnal) === '') {
            $this->addError('jurnal', 'Jurnal wajib diisi');

            return;
        }

        $existing = AbsensiPendidik::query()
            ->where('jadwal_id', $slot->id)
            ->where('pendidik_id', $this->jurnalPendidikId)
            ->first();

        if ($existing?->datang === null) {
            $this->addError('jurnal', 'Belum absen datang');

            return;
        }

        if ($existing->pulang !== null) {
            $this->addError('jurnal', 'Sudah absen pulang');

            return;
        }

        $existing->update([
            'pulang' => now(),
            'jurnal' => $this->jurnal,
        ]);

        $this->pesan = $this->jurnalNama.' — Pulang';
        $this->tutupJurnal();
        $this->fokusToken();
    }

    public function tutupJurnal(): void
    {
        $this->reset(['jurnal', 'showJurnalModal', 'jurnalNama', 'jurnalPendidikId']);
        $this->resetErrorBag('jurnal');
    }

    public function simpanIzin(): void
    {
        $slot = $this->slotAktif();
        $kelas = $slot->kelas;
        $allowedIds = AdminVisibility::pelajarForKelas($kelas)->pluck('id')
            ->merge(AdminVisibility::pendidikForKelas($kelas)->pluck('id'))
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
        $isPelajar = (int) $user->role_id === 4;

        if ($isPelajar) {
            $existing = AbsensiPelajar::query()
                ->where('jadwal_id', $slot->id)
                ->where('pelajar_id', $user->id)
                ->first();
        } else {
            $existing = AbsensiPendidik::query()
                ->where('jadwal_id', $slot->id)
                ->where('pendidik_id', $user->id)
                ->first();
        }

        if ($existing && $existing->datang !== null) {
            $this->addError('izin_user_id', 'Sudah hadir, ubah lewat scan pulang atau biarkan');

            return;
        }

        $payload = [
            'status' => (int) $this->izin_status,
            'keterangan' => trim($this->izin_keterangan) === '' ? null : $this->izin_keterangan,
        ];

        if ($isPelajar) {
            AbsensiPelajar::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pelajar_id' => $user->id],
                $payload
            );
        } else {
            AbsensiPendidik::updateOrCreate(
                ['jadwal_id' => $slot->id, 'pendidik_id' => $user->id],
                $payload
            );
        }

        $this->reset(['izin_user_id', 'izin_keterangan']);
        $this->izin_status = '2';
    }

    public function tandaiSisaAlpa(): void
    {
        $slot = $this->slotAktif();

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
        $this->slotAktif();
        $this->lewatiAlpa = true;
    }

    protected function fokusToken(): void
    {
        $this->dispatch('fokus-token');
        $this->js('document.getElementById("token")?.focus()');
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

        $hadirPendidik = $slot
            ? AbsensiPendidik::query()->where('jadwal_id', $slot->id)->with('pendidik')->orderByDesc('id')->get()
            : collect();
        $hadirPelajar = $slot
            ? AbsensiPelajar::query()->where('jadwal_id', $slot->id)->with('pelajar')->orderByDesc('id')->get()
            : collect();
        [$datangPendidik, $izinPendidik] = $this->pisahDatangIzin($hadirPendidik);
        [$datangPelajar, $izinPelajar] = $this->pisahDatangIzin($hadirPelajar);
        $pelajarList = $kelasAktif ? AdminVisibility::pelajarForKelas($kelasAktif)->get() : collect();

        return view('livewire.admin.absensi-slot', [
            'kelasList' => AdminVisibility::kelasForJadwal($actor)->with('markas')->get(),
            'slots' => $slots,
            'slot' => $slot,
            'datangPendidik' => $datangPendidik,
            'datangPelajar' => $datangPelajar,
            'izinPendidik' => $izinPendidik,
            'izinPelajar' => $izinPelajar,
            'pendidikList' => $kelasAktif ? AdminVisibility::pendidikForKelas($kelasAktif)->get() : collect(),
            'pelajarList' => $pelajarList,
            'adaSisaAlpa' => $slot ? $this->pelajarSisaAlpa($slot, $pelajarList, $hadirPelajar)->isNotEmpty() : false,
        ]);
    }

    private function pisahDatangIzin($rows): array
    {
        $izinStatus = [AbsensiStatus::IZIN, AbsensiStatus::SAKIT, AbsensiStatus::ALPA];

        return [
            $rows->filter(fn ($row) => $row->datang !== null)->values(),
            $rows->filter(fn ($row) => $row->datang === null && in_array((int) $row->status, $izinStatus, true))->values(),
        ];
    }

    private function pelajarSisaAlpa(Jadwal $slot, $pelajarList = null, $hadirPelajar = null)
    {
        $pelajarList = $pelajarList ?? AdminVisibility::pelajarForKelas($slot->kelas)->get();
        $absensiMap = ($hadirPelajar ?? AbsensiPelajar::query()->where('jadwal_id', $slot->id)->get())
            ->keyBy(fn ($row) => (int) $row->pelajar_id);

        return $pelajarList->filter(function ($siswa) use ($absensiMap) {
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
}
