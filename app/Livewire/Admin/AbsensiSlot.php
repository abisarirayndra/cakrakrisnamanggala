<?php

namespace App\Livewire\Admin;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use App\Support\AbsensiStatus;
use App\Support\AdminVisibility;
use App\User;
use Carbon\Carbon;
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

    public ?string $jurnalWaktu = null;

    public bool $showManual = false;

    public bool $showIzin = false;

    public string $manual_user_id = '';

    public string $manual_mode = 'datang';
    public bool $showEditModal = false;

    public string $edit_tipe = '';

    public ?int $edit_id = null;

    public string $edit_nama = '';

    public string $edit_status = '1';

    public string $edit_datang = '';

    public string $edit_pulang = '';

    public string $edit_keterangan = '';

    public string $edit_jurnal = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function updatedKelasId(): void
    {
        $this->jadwal_id = '';
        $this->reset(['token', 'jurnal', 'pesan', 'izin_user_id', 'izin_keterangan', 'lewatiAlpa', 'showJurnalModal', 'jurnalNama', 'jurnalPendidikId', 'jurnalWaktu', 'manual_user_id']);
        $this->tutupEdit();
        $this->izin_status = '2';
        $this->mode = 'datang';
        $this->resetErrorBag();
    }

    public function updatedJadwalId(): void
    {
        $this->reset(['token', 'jurnal', 'pesan', 'lewatiAlpa', 'showJurnalModal', 'jurnalNama', 'jurnalPendidikId', 'jurnalWaktu', 'manual_user_id']);
        $this->tutupEdit();
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

        if ($this->catatAbsensi($slot, $user, $this->mode, now(), 'token')) {
            $this->token = '';
        }
    }

    public function toggleManual(): void
    {
        $this->showManual = ! $this->showManual;
        $this->resetErrorBag(['manual_user_id', 'manual_mode']);
    }

    public function toggleIzin(): void
    {
        $this->showIzin = ! $this->showIzin;
        $this->resetErrorBag(['izin_user_id', 'izin_status', 'izin_keterangan']);
    }

    public function simpanManual(): void
    {
        $this->pesan = '';
        $slot = $this->slotAktif();
        $kelas = $slot->kelas;
        $allowedIds = AdminVisibility::pelajarForKelas($kelas)->pluck('id')
            ->merge(AdminVisibility::pendidikForKelas($kelas)->pluck('id'))
            ->map(fn ($id) => (string) $id)
            ->all();

        $this->validate([
            'manual_user_id' => 'required|in:'.implode(',', $allowedIds),
            'manual_mode' => 'required|in:datang,pulang',
        ], [
            'manual_user_id.required' => 'Pilih nama',
        ]);

        $user = User::query()->findOrFail($this->manual_user_id);

        if ($this->catatAbsensi($slot, $user, $this->manual_mode, now(), 'manual_user_id')) {
            $this->reset('manual_user_id');
        }

        if ($this->showJurnalModal) {
            $this->js('document.getElementById("jurnal")?.focus()');
        }
    }

    protected function catatAbsensi(Jadwal $slot, User $user, string $mode, Carbon $waktu, string $field): bool
    {
        $isPelajar = (int) $user->role_id === 4;
        $isPendidik = (int) $user->role_id === 3;

        if ($isPelajar && (int) $user->kelas_id !== (int) $slot->kelas_id) {
            $this->addError($field, 'Bukan pelajar kelas ini');

            return false;
        }

        if ($isPendidik && ! AdminVisibility::pendidikForKelas($slot->kelas)->whereKey($user->id)->exists()) {
            $this->addError($field, 'Bukan pendidik markas ini');

            return false;
        }

        if (! $isPelajar && ! $isPendidik) {
            $this->addError($field, 'Kartu tidak untuk absensi mapel');

            return false;
        }

        if ($mode === 'pulang') {
            return $this->catatPulang($slot, $user, $isPelajar, $waktu, $field);
        }

        if ($mode !== 'datang') {
            return false;
        }

        $model = $isPelajar ? AbsensiPelajar::class : AbsensiPendidik::class;
        $kolom = $isPelajar ? 'pelajar_id' : 'pendidik_id';
        $existing = $model::query()
            ->where('jadwal_id', $slot->id)
            ->where($kolom, $user->id)
            ->first();

        if ($existing?->datang !== null) {
            $this->addError($field, 'Sudah absen datang');

            return false;
        }

        $statusDatang = AbsensiStatus::dariDatang($waktu, $slot->mulai);

        $model::updateOrCreate(
            ['jadwal_id' => $slot->id, $kolom => $user->id],
            ['datang' => $waktu, 'status' => $statusDatang]
        );

        $this->pesan = $user->nama.' — '.AbsensiStatus::label($statusDatang);

        return true;
    }

    protected function catatPulang(Jadwal $slot, User $user, bool $isPelajar, Carbon $waktu, string $field): bool
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
            $this->addError($field, 'Belum absen datang');

            return false;
        }

        if ($existing->pulang !== null) {
            $this->addError($field, 'Sudah absen pulang');

            return false;
        }

        if ($waktu->lt($existing->datang)) {
            $this->addError($field, 'Jam pulang sebelum jam datang');

            return false;
        }

        if (! $isPelajar && ! $this->sudahAdaPendidikPulang($slot)) {
            $this->showJurnalModal = true;
            $this->jurnalNama = $user->nama;
            $this->jurnalPendidikId = (int) $user->id;
            $this->jurnalWaktu = $waktu->toDateTimeString();
            $this->jurnal = '';
            $this->resetErrorBag('jurnal');

            return true;
        }

        $existing->update([
            'pulang' => $waktu,
        ]);

        $this->pesan = $user->nama.' — Pulang';

        return true;
    }

    public function simpanJurnalPulang(): void
    {
        $slot = $this->slotAktif();
        abort_unless($this->showJurnalModal && $this->jurnalPendidikId, 403);
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

        if ($this->sudahAdaPendidikPulang($slot)) {
            $this->addError('jurnal', 'Jurnal sudah diisi pendidik lain');

            return;
        }

        $existing->update([
            'pulang' => $this->jurnalWaktu ? Carbon::parse($this->jurnalWaktu) : now(),
            'jurnal' => $this->jurnal,
        ]);

        $this->pesan = $this->jurnalNama.' — Pulang';
        $this->tutupJurnal();
        $this->fokusToken();
    }

    public function tutupJurnal(): void
    {
        $this->reset(['jurnal', 'showJurnalModal', 'jurnalNama', 'jurnalPendidikId', 'jurnalWaktu']);
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

    public function ubahAbsensi(string $tipe, int $id): void
    {
        $row = $this->absensiMilikSlot($tipe, $id);
        $status = (int) $row->status;

        $this->resetErrorBag();
        $this->showEditModal = true;
        $this->edit_tipe = $tipe;
        $this->edit_id = (int) $row->id;
        $this->edit_nama = (string) ($tipe === 'pendidik' ? $row->pendidik?->nama : $row->pelajar?->nama);
        $this->edit_status = in_array($status, [AbsensiStatus::IZIN, AbsensiStatus::SAKIT, AbsensiStatus::ALPA], true)
            ? (string) $status
            : (string) AbsensiStatus::HADIR;
        $this->edit_datang = $row->datang?->format('H:i') ?? '';
        $this->edit_pulang = $row->pulang?->format('H:i') ?? '';
        $this->edit_keterangan = (string) ($row->keterangan ?? '');
        $this->edit_jurnal = $tipe === 'pendidik' ? (string) ($row->jurnal ?? '') : '';
    }

    public function simpanEditAbsensi(): void
    {
        abort_unless($this->showEditModal && $this->edit_id, 403);

        $slot = $this->slotAktif();
        $row = $this->absensiMilikSlot($this->edit_tipe, $this->edit_id);
        $hadir = (int) $this->edit_status === AbsensiStatus::HADIR;

        $this->validate([
            'edit_status' => 'required|in:1,2,3,4',
            'edit_datang' => $hadir ? 'required|date_format:H:i' : 'nullable',
            'edit_pulang' => $hadir ? 'nullable|date_format:H:i|after:edit_datang' : 'nullable',
        ], [
            'edit_datang.required' => 'Jam datang wajib diisi',
            'edit_pulang.after' => 'Jam pulang harus setelah jam datang',
        ]);

        if ((int) $this->edit_status === AbsensiStatus::IZIN && trim($this->edit_keterangan) === '') {
            $this->addError('edit_keterangan', 'Keterangan wajib untuk izin');

            return;
        }

        $tanggal = $slot->mulai->toDateString();
        $payload = [
            'keterangan' => trim($this->edit_keterangan) === '' ? null : $this->edit_keterangan,
        ];

        if ($hadir) {
            $datang = Carbon::parse($tanggal.' '.$this->edit_datang);
            $payload['datang'] = $datang;
            $payload['pulang'] = $this->edit_pulang === '' ? null : Carbon::parse($tanggal.' '.$this->edit_pulang);
            $payload['status'] = AbsensiStatus::dariDatang($datang, $slot->mulai);
        } else {
            $payload['datang'] = null;
            $payload['pulang'] = null;
            $payload['status'] = (int) $this->edit_status;
        }

        if ($this->edit_tipe === 'pendidik') {
            $payload['jurnal'] = trim($this->edit_jurnal) === '' ? null : $this->edit_jurnal;
        }

        $row->update($payload);

        $this->pesan = $this->edit_nama.' — Absensi diperbarui';
        $this->tutupEdit();
    }

    public function hapusAbsensi(string $tipe, int $id): void
    {
        $row = $this->absensiMilikSlot($tipe, $id);
        $nama = $tipe === 'pendidik' ? $row->pendidik?->nama : $row->pelajar?->nama;
        $row->delete();

        if ($this->edit_id === $id && $this->edit_tipe === $tipe) {
            $this->tutupEdit();
        }

        $this->pesan = $nama.' — Absensi dihapus';
    }

    public function tutupEdit(): void
    {
        $this->reset(['showEditModal', 'edit_tipe', 'edit_id', 'edit_nama', 'edit_status', 'edit_datang', 'edit_pulang', 'edit_keterangan', 'edit_jurnal']);
        $this->resetErrorBag(['edit_status', 'edit_datang', 'edit_pulang', 'edit_keterangan', 'edit_jurnal']);
    }

    protected function absensiMilikSlot(string $tipe, int $id): AbsensiPendidik|AbsensiPelajar
    {
        abort_unless(in_array($tipe, ['pendidik', 'pelajar'], true), 404);

        $model = $tipe === 'pendidik' ? AbsensiPendidik::class : AbsensiPelajar::class;

        return $model::query()
            ->where('jadwal_id', $this->slotAktif()->id)
            ->whereKey($id)
            ->firstOrFail();
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

    protected function sudahAdaPendidikPulang(Jadwal $slot): bool
    {
        return AbsensiPendidik::query()
            ->where('jadwal_id', $slot->id)
            ->whereNotNull('pulang')
            ->exists();
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
