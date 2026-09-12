<?php

namespace App\Livewire\Admin;

use App\Jadwal;
use App\Kelas;
use App\Mapel;
use App\Support\AdminVisibility;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
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

    public function ubah(int $id): void
    {
        $row = $this->authorizeRow($id);

        $this->resetErrorBag();

        if ($row->sudahAdaAbsensi()) {
            $this->addError('jadwal', 'Jadwal sudah dipakai absensi');

            return;
        }

        $this->editId = $row->id;
        $this->kelas_id = (string) $row->kelas_id;
        $this->senin = $row->mulai->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->hari = (string) ($row->mulai->dayOfWeekIso - 1);
        $this->mapel_id = (string) $row->mapel_id;
        $this->pendidik_id = (string) $row->pendidik_id;
        $this->jam_mulai = $row->mulai->format('H:i');
        $this->jam_selesai = $row->selesai->format('H:i');
    }

    public function hapus(int $id): void
    {
        $row = $this->authorizeRow($id);

        if ($row->sudahAdaAbsensi()) {
            $this->addError('jadwal', 'Jadwal sudah dipakai absensi');

            return;
        }

        $row->delete();

        if ($this->editId === $id) {
            $this->batal();
        }
    }

    public function simpan(): void
    {
        $actor = auth()->user();
        $kelasVisible = AdminVisibility::kelasForJadwal($actor)->whereKey($this->kelas_id)->first();

        $this->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
                Rule::in(AdminVisibility::kelasForJadwal($actor)->pluck('id')->all()),
            ],
            'hari' => ['required', 'integer', 'between:0,6'],
            'mapel_id' => ['required', 'exists:mapels,id'],
            'pendidik_id' => [
                'required',
                'exists:users,id',
                Rule::in($kelasVisible
                    ? AdminVisibility::pendidikForKelas($kelasVisible)->pluck('users.id')->all()
                    : []),
            ],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $this->kelasTerpilih();

        $row = null;
        if ($this->editId !== null) {
            $row = $this->authorizeRow($this->editId);

            if ($row->sudahAdaAbsensi()) {
                $this->addError('jadwal', 'Jadwal sudah dipakai absensi');

                return;
            }
        }

        $mulai = Carbon::parse($this->senin)->startOfWeek(Carbon::MONDAY)
            ->addDays((int) $this->hari)
            ->setTimeFromTimeString($this->jam_mulai);
        $selesai = $mulai->copy()->setTimeFromTimeString($this->jam_selesai);

        $bentrok = AdminVisibility::jadwalQuery($actor)
            ->where('adm_jadwal.kelas_id', $this->kelas_id)
            ->where('adm_jadwal.id', '!=', $this->editId ?? 0)
            ->where('adm_jadwal.mulai', '<', $selesai)
            ->where('adm_jadwal.selesai', '>', $mulai)
            ->exists();

        if ($bentrok) {
            $this->addError('jam_mulai', 'Jam bentrok dengan slot lain');

            return;
        }

        if ($row !== null) {
            $row->update([
                'mapel_id' => $this->mapel_id,
                'pendidik_id' => $this->pendidik_id,
                'kelas_id' => $this->kelas_id,
                'mulai' => $mulai,
                'selesai' => $selesai,
            ]);
        } else {
            Jadwal::create([
                'staf_id' => auth()->id(),
                'mapel_id' => $this->mapel_id,
                'pendidik_id' => $this->pendidik_id,
                'kelas_id' => $this->kelas_id,
                'mulai' => $mulai,
                'selesai' => $selesai,
            ]);
        }

        $this->batal();
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

    private function kelasTerpilih(): Kelas
    {
        return AdminVisibility::kelasForJadwal(auth()->user())
            ->whereKey($this->kelas_id)
            ->firstOrFail();
    }

    private function authorizeRow(int $id): Jadwal
    {
        abort_unless(Jadwal::whereKey($id)->exists(), 404);

        $row = AdminVisibility::jadwalQuery(auth()->user())
            ->where('adm_jadwal.id', $id)
            ->first();

        abort_unless($row !== null, 403);

        return $row;
    }
}
