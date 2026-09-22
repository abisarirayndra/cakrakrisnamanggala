<?php

namespace App\Support;

use App\BankPaket;
use App\BankSoal;
use App\CatAnalisisCatatan;
use App\CatJadwal;
use App\CatSesi;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class CatAnalisisPaket
{
    public static function daftarUntuk(User $pendidik): Collection
    {
        $pendidikId = (int) $pendidik->id;

        return CatJadwal::query()
            ->whereHas('banks', fn ($q) => $q->where('pendidik_id', $pendidikId))
            ->with([
                'banks' => fn ($q) => $q->where('pendidik_id', $pendidikId),
                'sesi' => fn ($q) => $q->whereHas('bank', fn ($bank) => $bank->where('pendidik_id', $pendidikId)),
            ])
            ->orderByDesc('mulai')
            ->get()
            ->map(function (CatJadwal $jadwal) {
                $peserta = $jadwal->sesi->pluck('pelajar_id')->unique();

                return [
                    'id' => $jadwal->id,
                    'nama' => $jadwal->nama,
                    'waktu' => $jadwal->labelWaktuPelaksanaan(),
                    'banks' => $jadwal->banks->pluck('nama')->filter()->values(),
                    'peserta' => $peserta->count(),
                ];
            });
    }

    public static function milikPendidik(CatJadwal $jadwal, int $pendidikId): bool
    {
        return $jadwal->banks()->where('pendidik_id', $pendidikId)->exists();
    }

    public static function detail(CatJadwal $jadwal, int $pendidikId): array
    {
        $jadwal->load([
            'banks' => fn ($q) => $q->where('pendidik_id', $pendidikId)->with('soal.opsi'),
            'sesi' => fn ($q) => $q->whereHas('bank', fn ($bank) => $bank->where('pendidik_id', $pendidikId))
                ->with(['pelajar.kelas', 'jawaban']),
        ]);

        $banks = $jadwal->banks->values();
        $soalPerBank = $banks->mapWithKeys(
            fn (BankPaket $bank) => [$bank->id => $jadwal->daftarSoalBank((int) $bank->id)]
        );

        $siswa = $jadwal->sesi
            ->pluck('pelajar_id')
            ->unique()
            ->map(fn ($id) => self::barisSiswa(
                $jadwal->sesi->where('pelajar_id', $id),
                $banks,
                $soalPerBank
            ))
            ->sortBy([
                ['skor', 'desc'],
                ['nama', 'asc'],
            ])
            ->values();

        $catatan = CatAnalisisCatatan::query()
            ->where('jadwal_id', $jadwal->id)
            ->where('pendidik_id', $pendidikId)
            ->pluck('catatan', 'pelajar_id');

        $siswa = $siswa->map(function (array $item) use ($catatan) {
            $item['catatan'] = (string) ($catatan[$item['id']] ?? '');

            return $item;
        });

        $soal = self::analisisSoal($banks, $soalPerBank, $jadwal->sesi);
        $selesai = $siswa->where('status', 'Selesai');
        $skorSelesai = $selesai->pluck('skor');
        $terukur = $soal->filter(fn (array $item) => $item['persen_benar'] !== null);

        return [
            'jadwal' => $jadwal,
            'banks' => $banks,
            'siswa' => $siswa,
            'soal' => $soal,
            'ringkasan' => [
                'peserta' => $siswa->count(),
                'selesai' => $selesai->count(),
                'berjalan' => $siswa->where('status', 'Berjalan')->count(),
                'rata' => $skorSelesai->isEmpty() ? null : (int) round($skorSelesai->avg()),
                'tertinggi' => $skorSelesai->isEmpty() ? null : (int) $skorSelesai->max(),
                'terendah' => $skorSelesai->isEmpty() ? null : (int) $skorSelesai->min(),
                'tersulit' => $terukur->sortBy('persen_benar')->first(),
                'termudah' => $terukur->sortByDesc('persen_benar')->first(),
            ],
        ];
    }

    private static function barisSiswa(Collection $sesiPelajar, Collection $banks, Collection $soalPerBank): array
    {
        $user = $sesiPelajar->first()?->pelajar;
        $hasil = collect();
        $benar = $salah = $kosong = $skor = 0;
        $detik = 0;
        $statusBank = [];
        $nomor = 1;

        foreach ($banks as $bank) {
            $sesi = $sesiPelajar->firstWhere('bank_paket_id', $bank->id);
            $statusBank[] = $sesi ? ($sesi->sudahSelesai() ? 'Selesai' : 'Berjalan') : 'Belum';
            $skor += (int) ($sesi?->skorLive() ?? 0);

            if ($sesi?->started_at && $sesi->submitted_at) {
                $detik += max(0, (int) $sesi->started_at->diffInSeconds($sesi->submitted_at));
            }

            foreach ($soalPerBank[$bank->id] as $item) {
                $baris = self::hasilSoal($item, $sesi, $bank, $nomor++);
                $hasil->push($baris);
                $benar += $baris['hasil'] === 'Benar' ? 1 : 0;
                $salah += $baris['hasil'] === 'Salah' ? 1 : 0;
                $kosong += $baris['hasil'] === 'Kosong' ? 1 : 0;
            }
        }

        $status = collect($statusBank)->every(fn ($status) => $status === 'Selesai')
            ? 'Selesai'
            : (collect($statusBank)->contains(fn ($status) => $status !== 'Belum') ? 'Berjalan' : 'Belum');

        return [
            'id' => $user?->id,
            'nama' => $user?->nama ?? '-',
            'kelas' => $user?->kelas?->nama ?? '-',
            'status' => $status,
            'benar' => $benar,
            'salah' => $salah,
            'kosong' => $kosong,
            'skor' => $skor,
            'durasi' => self::labelDurasi($detik),
            'benar_di' => $hasil->where('hasil', 'Benar')->pluck('nomor')->join(', ') ?: '—',
            'salah_di' => $hasil->where('hasil', 'Salah')->pluck('nomor')->join(', ') ?: '—',
            'kosong_di' => $hasil->where('hasil', 'Kosong')->pluck('nomor')->join(', ') ?: '—',
            'soal' => $hasil,
        ];
    }

    private static function hasilSoal(BankSoal $soal, ?CatSesi $sesi, BankPaket $bank, int $nomor): array
    {
        $jawab = $sesi?->jawaban?->firstWhere('bank_soal_id', $soal->id);
        $kosong = ! filled($jawab?->kode);
        $benar = $sesi ? $sesi->apakahBenar($soal, $jawab, $bank->tipe) : false;

        return [
            'nomor' => $nomor,
            'bank' => $bank->nama,
            'soal' => self::ringkasSoal($soal->soal),
            'kunci' => self::kunciLabel($soal, $bank->tipe),
            'jawaban' => $kosong ? '—' : strtoupper((string) $jawab->kode),
            'hasil' => $sesi === null || $kosong ? 'Kosong' : ($benar ? 'Benar' : 'Salah'),
            'poin' => (int) ($jawab?->poin ?? 0),
            'poin_maks' => self::poinMaks($soal, $bank->tipe),
        ];
    }

    private static function analisisSoal(Collection $banks, Collection $soalPerBank, Collection $sesi): Collection
    {
        $hasil = collect();
        $nomor = 1;

        foreach ($banks as $bank) {
            $selesai = $sesi
                ->where('bank_paket_id', $bank->id)
                ->where('status', CatSesi::SELESAI)
                ->values();
            $jumlah = $selesai->count();

            foreach ($soalPerBank[$bank->id] as $item) {
                $benar = 0;
                $salah = 0;
                $kosong = 0;
                $pilihan = [];

                foreach ($selesai as $satu) {
                    $jawab = $satu->jawaban->firstWhere('bank_soal_id', $item->id);
                    $baris = self::hasilSoal($item, $satu, $bank, $nomor);
                    $benar += $baris['hasil'] === 'Benar' ? 1 : 0;
                    $salah += $baris['hasil'] === 'Salah' ? 1 : 0;
                    $kosong += $baris['hasil'] === 'Kosong' ? 1 : 0;
                    if (filled($jawab?->kode)) {
                        $kode = strtoupper((string) $jawab->kode);
                        $pilihan[$kode] = ($pilihan[$kode] ?? 0) + 1;
                    }
                }

                arsort($pilihan);
                $terbanyak = array_key_first($pilihan);

                $hasil->push([
                    'nomor' => $nomor++,
                    'bank' => $bank->nama,
                    'soal' => self::ringkasSoal($item->soal),
                    'kunci' => self::kunciLabel($item, $bank->tipe),
                    'benar' => $benar,
                    'salah' => $salah,
                    'kosong' => $kosong,
                    'peserta' => $jumlah,
                    'persen_benar' => $jumlah === 0 ? null : (int) round(($benar / $jumlah) * 100),
                    'pilihan_terbanyak' => $terbanyak,
                ]);
            }
        }

        return $hasil;
    }

    private static function kunciLabel(BankSoal $soal, string $tipe): string
    {
        if ($tipe === BankSoalTipe::PEMBOBOTAN) {
            return (string) ($soal->opsi->sortByDesc('poin')->first()?->kode ?: '—');
        }

        return strtoupper((string) ($soal->kunci ?: '—'));
    }

    private static function poinMaks(BankSoal $soal, string $tipe): int
    {
        if ($tipe === BankSoalTipe::PEMBOBOTAN) {
            return (int) $soal->opsi->max('poin');
        }

        return (int) ($soal->poin ?? 0);
    }

    private static function ringkasSoal(?string $soal): string
    {
        $teks = trim(preg_replace('/\$+/', '', (string) $soal) ?? '');

        return Str::limit($teks !== '' ? $teks : '—', 90);
    }

    private static function labelDurasi(int $detik): string
    {
        if ($detik <= 0) {
            return '—';
        }

        $menit = intdiv($detik, 60);
        $sisa = $detik % 60;

        if ($menit > 0 && $sisa > 0) {
            return $menit.' mnt '.$sisa.' dtk';
        }

        if ($menit > 0) {
            return $menit.' mnt';
        }

        return $sisa.' dtk';
    }
}
