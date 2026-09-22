<?php

namespace App;

use App\Support\BankSoalTipe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class CatSesi extends Model
{
    public const BERJALAN = 'berjalan';

    public const SELESAI = 'selesai';

    protected $table = 'cat_jadwal_sesi';

    protected $fillable = [
        'jadwal_id',
        'pelajar_id',
        'bank_paket_id',
        'status',
        'nilai',
        'urutan_soal',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'urutan_soal' => 'array',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(CatJadwal::class, 'jadwal_id');
    }

    public function pelajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelajar_id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(BankPaket::class, 'bank_paket_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(CatJawaban::class, 'sesi_id');
    }

    public function sudahSelesai(): bool
    {
        return $this->status === self::SELESAI;
    }

    public function kumpulkanSekarang(): void
    {
        if ($this->sudahSelesai()) {
            return;
        }

        $this->update([
            'status' => self::SELESAI,
            'nilai' => (int) $this->jawaban()->sum('poin'),
            'submitted_at' => now(),
        ]);
    }

    public function skorLive(): int
    {
        if ($this->sudahSelesai()) {
            return (int) $this->nilai;
        }

        if ($this->relationLoaded('jawaban')) {
            return (int) $this->jawaban->sum('poin');
        }

        return (int) $this->jawaban()->sum('poin');
    }

    public function laporan(Collection $soal, ?string $tipe = null): array
    {
        $jawaban = $this->relationLoaded('jawaban')
            ? $this->jawaban->keyBy('bank_soal_id')
            : $this->jawaban()->get()->keyBy('bank_soal_id');
        $benar = 0;

        foreach ($soal as $item) {
            if ($this->jawabanBenar($item, $jawaban->get($item->id), $tipe)) {
                $benar++;
            }
        }

        $jumlah = $soal->count();

        return [
            'benar' => $benar,
            'salah' => $jumlah - $benar,
            'skor' => $this->skorLive(),
            'jumlah' => $jumlah,
        ];
    }

    public function apakahBenar(BankSoal $soal, ?CatJawaban $jawab, ?string $tipe = null): bool
    {
        if (! filled($jawab?->kode)) {
            return false;
        }

        $tipeSoal = $tipe ?? $soal->paket?->tipe;

        if ($tipeSoal === BankSoalTipe::PEMBOBOTAN) {
            $max = (int) $soal->opsi->max('poin');
            $pilih = $soal->opsi->firstWhere('kode', $jawab->kode);

            return $pilih !== null && $max > 0 && (int) $pilih->poin === $max;
        }

        return strtoupper((string) $jawab->kode) === strtoupper((string) $soal->kunci);
    }

    public function pastikanUrutan(Collection $soal): void
    {
        $ids = $soal->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $tersimpan = collect($this->urutan_soal ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => in_array($id, $ids, true))
            ->values()
            ->all();
        $baru = array_values(array_diff($ids, $tersimpan));

        if ($tersimpan === []) {
            shuffle($baru);
            $this->urutan_soal = $baru;
            $this->save();

            return;
        }

        if ($baru !== []) {
            shuffle($baru);
            $this->urutan_soal = array_merge($tersimpan, $baru);
            $this->save();
        }
    }

    public function urutkanSoal(Collection $soal): Collection
    {
        $this->pastikanUrutan($soal);
        $byId = $soal->keyBy(fn (BankSoal $item) => (int) $item->id);

        return collect($this->urutan_soal)
            ->map(fn ($id) => $byId->get((int) $id))
            ->filter()
            ->values();
    }

    private function jawabanBenar(BankSoal $soal, ?CatJawaban $jawab, ?string $tipe): bool
    {
        return $this->apakahBenar($soal, $jawab, $tipe);
    }
}
