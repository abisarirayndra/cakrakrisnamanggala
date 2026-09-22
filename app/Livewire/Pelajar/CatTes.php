<?php

namespace App\Livewire\Pelajar;

use App\BankPaket;
use App\BankSoal;
use App\CatJadwal;
use App\CatJawaban;
use App\CatSesi;
use App\Support\BankSoalTipe;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel-pelajar')]
#[Title('Tes CAT')]
class CatTes extends Component
{
    public int $jadwalId;

    public ?int $bankId = null;

    public int $nomor = 0;

    public array $jawaban = [];

    public function boot(): void
    {
        abort_unless(auth()->user()?->isPelajar(), 403);
    }

    public function mount(int|string|CatJadwal $jadwal, int|string|BankPaket|null $bank = null): void
    {
        $this->jadwalId = $jadwal instanceof CatJadwal ? (int) $jadwal->id : (int) $jadwal;
        $model = $this->jadwal();

        $punyaAkses = (int) session('cat_akses_jadwal') === (int) $model->id;
        $punyaSesi = $model->sesi()->where('pelajar_id', auth()->id())->exists();

        if (! $punyaSesi && ! $punyaAkses) {
            abort(403);
        }

        if (now()->lt($model->mulai) && ! $punyaSesi) {
            abort(redirect()->route('pelajar.masukkan_token'));
        }

        if (now()->gte($model->selesai)) {
            $model->kumpulkanSesiBerjalan((int) auth()->id());
        }

        $this->bankId = $bank instanceof BankPaket ? (int) $bank->id : ($bank !== null ? (int) $bank : null);

        if ($this->bankId === null && $model->banks->count() === 1) {
            $this->bankId = (int) $model->banks->first()->id;
        }

        if ($this->bankId !== null) {
            $this->siapkanSesi($model);
        }
    }

    public function pilihBank(int $bankId): void
    {
        $model = $this->jadwal();
        if (! $model->banks->contains('id', $bankId)) {
            return;
        }

        if (now()->gte($model->selesai)) {
            $model->kumpulkanSesiBerjalan((int) auth()->id());
        }

        $this->bankId = $bankId;
        $this->nomor = 0;
        $this->siapkanSesi($model);
    }

    public function kePilihan(): void
    {
        if ($this->jadwal()->banks->count() < 2) {
            return;
        }

        $this->bankId = null;
        $this->nomor = 0;
        $this->jawaban = [];
    }

    public function pilihJawaban(string $kode): void
    {
        $sesi = $this->sesi();
        if (! $sesi || $sesi->sudahSelesai()) {
            return;
        }

        $soal = $this->soalAktif();
        if (! $soal) {
            return;
        }

        $kode = strtoupper($kode);
        $opsi = $soal->opsi->firstWhere('kode', $kode);
        if (! $opsi) {
            return;
        }

        $poin = 0;
        $tipe = $soal->paket?->tipe;
        if ($tipe === BankSoalTipe::TUNGGAL) {
            $poin = $kode === $soal->kunci ? (int) $soal->poin : 0;
        } else {
            $poin = (int) $opsi->poin;
        }

        CatJawaban::query()->updateOrCreate(
            ['sesi_id' => $sesi->id, 'bank_soal_id' => $soal->id],
            ['kode' => $kode, 'poin' => $poin]
        );

        $this->jawaban[$soal->id] = $kode;
    }

    public function keSoal(int $index): void
    {
        $jumlah = $this->daftarSoal()->count();
        if ($index < 0 || $index >= $jumlah) {
            return;
        }

        $this->nomor = $index;
    }

    public function kumpulkan(): void
    {
        $jadwal = $this->jadwal();
        if ($this->bankId === null) {
            $jadwal->kumpulkanSesiBerjalan((int) auth()->id());
        } else {
            $sesi = $this->sesi();
            $sesi?->kumpulkanSekarang();
        }

        if ($jadwal->semuaSelesaiUntuk((int) auth()->id())) {
            session()->forget('cat_akses_jadwal');
        }
    }

    public function render()
    {
        $jadwal = $this->jadwal();
        $soal = $this->daftarSoal();
        $sesi = $this->sesi();
        $sisaDetik = max(0, $jadwal->selesai->getTimestamp() - now()->getTimestamp());
        $totalDetik = max(1, $jadwal->selesai->getTimestamp() - $jadwal->mulai->getTimestamp());
        $ringkasan = $jadwal->ringkasanUntuk((int) auth()->id());

        return view('livewire.pelajar.cat-tes', [
            'jadwal' => $jadwal,
            'daftarBank' => $jadwal->banks,
            'bankAktif' => $this->bankId ? $jadwal->banks->firstWhere('id', $this->bankId) : null,
            'daftarSoal' => $soal,
            'soalAktif' => $soal->get($this->nomor),
            'sesi' => $sesi,
            'ringkasan' => $ringkasan,
            'laporan' => $sesi?->sudahSelesai() ? $sesi->laporan($soal) : null,
            'matematis' => $soal->contains(fn (BankSoal $item) => (bool) $item->paket?->isMatematis()),
            'sisaDetik' => $sisaDetik,
            'totalDetik' => $totalDetik,
        ]);
    }

    private function jadwal(): CatJadwal
    {
        return CatJadwal::query()
            ->with(['banks.soal.opsi', 'sesi.pelajar.kelas', 'sesi.jawaban'])
            ->findOrFail($this->jadwalId);
    }

    private function sesi(): ?CatSesi
    {
        if ($this->bankId === null) {
            return null;
        }

        return CatSesi::query()
            ->where('jadwal_id', $this->jadwalId)
            ->where('pelajar_id', auth()->id())
            ->where('bank_paket_id', $this->bankId)
            ->first();
    }

    private function daftarSoal()
    {
        if ($this->bankId === null) {
            return collect();
        }

        $soal = $this->jadwal()->daftarSoalBank($this->bankId);
        $sesi = $this->sesi();

        return $sesi ? $sesi->urutkanSoal($soal) : $soal;
    }

    private function soalAktif(): ?BankSoal
    {
        $soal = $this->daftarSoal()->get($this->nomor);

        return $soal instanceof BankSoal ? $soal : null;
    }

    private function siapkanSesi(CatJadwal $model): void
    {
        if ($this->bankId === null || ! $model->banks->contains('id', $this->bankId)) {
            abort(404);
        }

        $sesi = CatSesi::query()
            ->where('jadwal_id', $model->id)
            ->where('pelajar_id', auth()->id())
            ->where('bank_paket_id', $this->bankId)
            ->first();

        if (! $sesi) {
            if (now()->lt($model->mulai) || now()->gte($model->selesai)) {
                abort(redirect()->route('pelajar.masukkan_token'));
            }

            $sesi = CatSesi::create([
                'jadwal_id' => $model->id,
                'pelajar_id' => auth()->id(),
                'bank_paket_id' => $this->bankId,
                'status' => CatSesi::BERJALAN,
                'started_at' => now(),
            ]);
        }

        $sesi->pastikanUrutan($model->daftarSoalBank((int) $this->bankId));
        $this->muatJawaban($sesi);

        if (! $sesi->sudahSelesai() && now()->gte($model->selesai)) {
            $sesi->kumpulkanSekarang();
        }
    }

    private function muatJawaban(CatSesi $sesi): void
    {
        $this->jawaban = $sesi->jawaban()
            ->get()
            ->mapWithKeys(fn (CatJawaban $row) => [$row->bank_soal_id => $row->kode])
            ->all();
    }
}
