<?php

namespace App\Imports;

use App\BankOpsi;
use App\BankPaket;
use App\BankSoal;
use App\Support\BankSoalTipe;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankSoalImport implements ToCollection, WithHeadingRow
{
    public int $jumlah = 0;

    public function __construct(private BankPaket $paket, private User $pendidik) {}

    public function collection(Collection $rows): void
    {
        if ($rows->isNotEmpty() && ! $this->punyaKolom($rows->first(), 'soal')) {
            throw ValidationException::withMessages([
                'fileImpor' => 'Kolom template tidak sesuai. Unduh template ulang.',
            ]);
        }

        $siap = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $baris = $index + 2;
            $hasil = $this->paket->tipe === BankSoalTipe::PEMBOBOTAN
                ? $this->parsePembobotan($row, $baris)
                : $this->parseTunggal($row, $baris);

            if ($hasil === null) {
                continue;
            }

            if (isset($hasil['errors'])) {
                array_push($errors, ...$hasil['errors']);
                continue;
            }

            $siap[] = $hasil;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages(['fileImpor' => $errors]);
        }

        if ($siap === []) {
            throw ValidationException::withMessages([
                'fileImpor' => 'Tidak ada soal yang bisa diimpor.',
            ]);
        }

        DB::transaction(function () use ($siap) {
            foreach ($siap as $item) {
                $this->simpan($item);
            }
        });

        $this->jumlah = count($siap);
    }

    private function parseTunggal(Collection $row, int $baris): ?array
    {
        $soal = $this->teks($row, 'soal');
        $poinTeks = $this->teks($row, 'poin');
        $kunci = strtoupper($this->teks($row, 'kunci'));
        $opsi = $this->opsiTeks($row);

        if ($this->barisKosong($soal, $poinTeks, $kunci, $opsi)) {
            return null;
        }

        $errors = [];

        if ($soal === '') {
            $errors[] = "Baris {$baris}: Soal wajib diisi.";
        }

        if ($poinTeks === '' || ! is_numeric($poinTeks) || (int) $poinTeks < 0) {
            $errors[] = "Baris {$baris}: Poin wajib angka 0 atau lebih.";
        }

        if (! in_array($kunci, ['A', 'B', 'C', 'D', 'E'], true)) {
            $errors[] = "Baris {$baris}: Kunci harus A, B, C, D, atau E.";
        }

        foreach (['A', 'B', 'C', 'D'] as $kode) {
            if ($opsi[$kode] === '') {
                $errors[] = "Baris {$baris}: Opsi {$kode} wajib diisi.";
            }
        }

        if ($kunci === 'E' && $opsi['E'] === '') {
            $errors[] = "Baris {$baris}: Opsi E harus diisi jika menjadi kunci.";
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        return [
            'soal' => $soal,
            'poin' => (int) $poinTeks,
            'kunci' => $kunci,
            'opsi' => $this->opsiPayload($opsi, []),
        ];
    }

    private function parsePembobotan(Collection $row, int $baris): ?array
    {
        $soal = $this->teks($row, 'soal');
        $opsi = $this->opsiTeks($row);
        $poin = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $kode) {
            $poin[$kode] = $this->teks($row, 'poin_'.strtolower($kode));
        }

        if ($this->barisKosong($soal, implode('', $poin), '', $opsi)) {
            return null;
        }

        $errors = [];

        if ($soal === '') {
            $errors[] = "Baris {$baris}: Soal wajib diisi.";
        }

        foreach (['A', 'B', 'C', 'D'] as $kode) {
            if ($opsi[$kode] === '') {
                $errors[] = "Baris {$baris}: Opsi {$kode} wajib diisi.";
            }
            if ($poin[$kode] === '' || ! is_numeric($poin[$kode])) {
                $errors[] = "Baris {$baris}: Skor opsi {$kode} wajib angka.";
            }
        }

        if ($opsi['E'] !== '' && ($poin['E'] === '' || ! is_numeric($poin['E']))) {
            $errors[] = "Baris {$baris}: Skor opsi E wajib angka.";
        }

        if ($errors !== []) {
            return ['errors' => $errors];
        }

        return [
            'soal' => $soal,
            'poin' => null,
            'kunci' => null,
            'opsi' => $this->opsiPayload($opsi, $poin),
        ];
    }

    private function simpan(array $item): void
    {
        $soal = BankSoal::create([
            'paket_id' => $this->paket->id,
            'pendidik_id' => $this->pendidik->id,
            'mapel_id' => $this->pendidik->pendidik?->mapel_id,
            'soal' => $item['soal'],
            'poin' => $item['poin'],
            'kunci' => $item['kunci'],
        ]);

        foreach ($item['opsi'] as $urutan => $opsi) {
            BankOpsi::create([
                'bank_soal_id' => $soal->id,
                'kode' => $opsi['kode'],
                'teks' => $opsi['teks'],
                'poin' => $opsi['poin'],
                'urutan' => $urutan + 1,
            ]);
        }
    }

    private function opsiTeks(Collection $row): array
    {
        $opsi = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $kode) {
            $opsi[$kode] = $this->teks($row, 'opsi_'.strtolower($kode));
        }

        return $opsi;
    }

    private function opsiPayload(array $opsi, array $poin): array
    {
        $hasil = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $kode) {
            if ($opsi[$kode] === '') {
                continue;
            }

            $hasil[] = [
                'kode' => $kode,
                'teks' => $opsi[$kode],
                'poin' => isset($poin[$kode]) && $poin[$kode] !== '' ? (int) $poin[$kode] : 0,
            ];
        }

        return $hasil;
    }

    private function barisKosong(string $soal, string $poin, string $kunci, array $opsi): bool
    {
        return $soal === '' && $poin === '' && $kunci === '' && implode('', $opsi) === '';
    }

    private function teks(Collection $row, string $kolom): string
    {
        $nilai = $row->get($kolom);
        if ($nilai === null || $nilai === '') {
            return '';
        }

        if (is_numeric($nilai)) {
            $angka = 0 + $nilai;
            if ((int) $angka == $angka) {
                return (string) (int) $angka;
            }

            return (string) $angka;
        }

        return trim((string) $nilai);
    }

    private function punyaKolom(Collection $row, string $kolom): bool
    {
        return $row->has($kolom);
    }
}
