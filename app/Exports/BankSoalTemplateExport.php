<?php

namespace App\Exports;

use App\Support\BankSoalTipe;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class BankSoalTemplateExport implements FromArray, WithHeadings, WithStrictNullComparison
{
    public function __construct(private string $tipe) {}

    public function headings(): array
    {
        return $this->tipe === BankSoalTipe::PEMBOBOTAN
            ? ['soal', 'opsi_a', 'poin_a', 'opsi_b', 'poin_b', 'opsi_c', 'poin_c', 'opsi_d', 'poin_d', 'opsi_e', 'poin_e']
            : ['soal', 'poin', 'kunci', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e'];
    }

    public function array(): array
    {
        if ($this->tipe === BankSoalTipe::PEMBOBOTAN) {
            return [[
                'Sikap yang paling tepat',
                'Menolong', 5,
                'Diam', 3,
                'Menyindir', 1,
                'Mengabaikan', 0,
                'Mengejek', -1,
            ]];
        }

        return [[
            'Hasil dari 1+1 adalah',
            10,
            'B',
            '1',
            '2',
            '3',
            '4',
            '5',
        ]];
    }
}
