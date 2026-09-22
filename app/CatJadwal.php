<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class CatJadwal extends Model
{
    protected $table = 'cat_jadwal';

    protected $fillable = [
        'admin_id',
        'nama',
        'mulai',
        'selesai',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'datetime',
            'selesai' => 'datetime',
        ];
    }

    public function banks(): BelongsToMany
    {
        return $this->belongsToMany(BankPaket::class, 'cat_jadwal_paket', 'jadwal_id', 'bank_paket_id')
            ->withPivot('urutan')
            ->orderByPivot('urutan');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function sesi(): HasMany
    {
        return $this->hasMany(CatSesi::class, 'jadwal_id');
    }

    public function pasangBanks(iterable $paketIds): void
    {
        $sync = [];
        $urutan = 1;
        foreach ($paketIds as $id) {
            $sync[(int) $id] = ['urutan' => $urutan++];
        }

        $this->banks()->sync($sync);
    }

    public function daftarSoal(): Collection
    {
        $this->loadMissing(['banks.soal.opsi']);

        return $this->banks
            ->flatMap(function (BankPaket $bank) {
                return $this->soalBank($bank);
            })
            ->values();
    }

    public function daftarSoalBank(int $bankId): Collection
    {
        $this->loadMissing(['banks.soal.opsi']);
        $bank = $this->banks->firstWhere('id', $bankId);

        return $bank instanceof BankPaket ? $this->soalBank($bank) : collect();
    }

    public function semuaSelesaiUntuk(int $pelajarId): bool
    {
        $bankIds = $this->banks()->pluck('cat_bank_paket.id');
        if ($bankIds->isEmpty()) {
            return false;
        }

        $selesai = $this->sesi()
            ->where('pelajar_id', $pelajarId)
            ->where('status', CatSesi::SELESAI)
            ->pluck('bank_paket_id');

        return $bankIds->diff($selesai)->isEmpty();
    }

    public function kumpulkanSesiBerjalan(int $pelajarId): void
    {
        $this->sesi()
            ->where('pelajar_id', $pelajarId)
            ->where('status', CatSesi::BERJALAN)
            ->get()
            ->each(fn (CatSesi $sesi) => $sesi->kumpulkanSekarang());
    }

    public static function historiPelajar(int $pelajarId): Collection
    {
        $ids = CatSesi::query()
            ->where('pelajar_id', $pelajarId)
            ->where('status', CatSesi::SELESAI)
            ->pluck('jadwal_id')
            ->unique()
            ->all();

        if ($ids === []) {
            return collect();
        }

        return static::query()
            ->with([
                'banks.soal.opsi',
                'sesi' => fn ($q) => $q->where('pelajar_id', $pelajarId)->with(['jawaban', 'pelajar.kelas']),
            ])
            ->whereIn('id', $ids)
            ->get()
            ->filter(function (self $jadwal) {
                $bankIds = $jadwal->banks->pluck('id');
                if ($bankIds->isEmpty()) {
                    return false;
                }

                $selesai = $jadwal->sesi
                    ->where('status', CatSesi::SELESAI)
                    ->pluck('bank_paket_id');

                return $bankIds->diff($selesai)->isEmpty();
            })
            ->map(function (self $jadwal) use ($pelajarId) {
                $ringkasan = $jadwal->ringkasanUntuk($pelajarId);
                $selesaiPada = $jadwal->sesi
                    ->where('status', CatSesi::SELESAI)
                    ->sortByDesc('submitted_at')
                    ->first()
                    ?->submitted_at;

                return [
                    'id' => $jadwal->id,
                    'nama' => $jadwal->nama,
                    'waktu' => $jadwal->labelWaktuPelaksanaan(),
                    'selesai_pada' => $selesaiPada,
                    'label_tanggal' => $selesaiPada?->isoFormat('D MMM Y')
                        ?: ($jadwal->mulai?->isoFormat('D MMM Y') ?: '—'),
                    'total' => $ringkasan['total'],
                ];
            })
            ->sortByDesc(fn (array $item) => $item['selesai_pada']?->timestamp ?? 0)
            ->values();
    }

    public function ringkasanPelajar(): Collection
    {
        $this->loadMissing(['banks.soal.opsi', 'sesi.pelajar.kelas', 'sesi.jawaban']);

        return $this->sesi
            ->pluck('pelajar_id')
            ->unique()
            ->map(fn ($id) => $this->ringkasanUntuk((int) $id))
            ->sortBy([
                ['total', 'desc'],
                ['nama', 'asc'],
            ])
            ->values();
    }

    public function ringkasanUntuk(int $pelajarId): array
    {
        $this->loadMissing(['banks.soal.opsi', 'sesi.pelajar.kelas', 'sesi.jawaban']);
        $sesiPelajar = $this->sesi->where('pelajar_id', $pelajarId);
        $user = $sesiPelajar->first()?->pelajar;

        $banks = $this->banks->map(function (BankPaket $bank) use ($sesiPelajar) {
            $sesi = $sesiPelajar->firstWhere('bank_paket_id', $bank->id);
            $soal = $this->soalBank($bank);

            return [
                'id' => $bank->id,
                'nama' => $bank->nama,
                'jumlah' => $soal->count(),
                'status' => $sesi ? ($sesi->sudahSelesai() ? 'Selesai' : 'Berjalan') : 'Belum',
                'skor' => $sesi?->skorLive(),
                'laporan' => $sesi ? $sesi->laporan($soal, $bank->tipe) : null,
            ];
        })->values();

        $statusList = $banks->pluck('status');
        $status = $statusList->every(fn ($status) => $status === 'Selesai')
            ? 'Selesai'
            : ($statusList->contains('Berjalan') || $statusList->contains('Selesai') ? 'Berjalan' : 'Belum');

        return [
            'pelajar_id' => $pelajarId,
            'nama' => $user?->nama ?? '-',
            'kelas' => $user?->kelas?->nama ?? '-',
            'status' => $status,
            'banks' => $banks,
            'total' => $banks->sum(fn (array $bank) => (int) ($bank['skor'] ?? 0)),
        ];
    }

    private function soalBank(BankPaket $bank): Collection
    {
        return $bank->soal->sortBy('id')->values()->each(function (BankSoal $soal) use ($bank) {
            $soal->setRelation('paket', $bank);
        });
    }

    public function teksChat(): string
    {
        return implode("\n", [
            'Nama Paket: '.$this->nama,
            'Waktu Pelaksanaan: '.$this->labelWaktuPelaksanaan(),
            'Total Waktu: '.$this->labelTotalWaktu(),
            'Token: '.$this->token,
        ]);
    }

    public function labelWaktuPelaksanaan(): string
    {
        if (! $this->mulai || ! $this->selesai) {
            return '-';
        }

        if ($this->mulai->isSameDay($this->selesai)) {
            return $this->mulai->isoFormat('dddd, D MMMM Y').' pukul '.$this->mulai->isoFormat('HH.mm').'–'.$this->selesai->isoFormat('HH.mm');
        }

        return $this->mulai->isoFormat('dddd, D MMMM Y [pukul] HH.mm').' – '.$this->selesai->isoFormat('dddd, D MMMM Y [pukul] HH.mm');
    }

    public function labelTotalWaktu(): string
    {
        if (! $this->mulai || ! $this->selesai) {
            return '-';
        }

        $menit = max(0, (int) round(abs($this->mulai->diffInMinutes($this->selesai))));
        $jam = intdiv($menit, 60);
        $sisa = $menit % 60;

        if ($jam > 0 && $sisa > 0) {
            return $jam.' jam '.$sisa.' menit';
        }

        if ($jam > 0) {
            return $jam.' jam';
        }

        return $sisa.' menit';
    }
}
