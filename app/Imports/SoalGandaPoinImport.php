<?php

namespace App\Imports;

use App\SoalDinasGandaPoin;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoalGandaPoinImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SoalDinasGandaPoin([
            'dn_tes_id' => $row['kode_tes'],
            'nomor_soal' => $row['nomor_soal'],
            'soal' => $row['soal'],
            'opsi_a' => $row['opsi_a'],
            'poin_a' => $row['poin_a'],
            'opsi_b' => $row['opsi_b'],
            'poin_a' => $row['poin_b'],
            'opsi_c' => $row['opsi_c'],
            'poin_a' => $row['poin_c'],
            'opsi_d' => $row['opsi_d'],
            'poin_a' => $row['poin_d'],
            'opsi_e' => $row['opsi_e'],
            'poin_a' => $row['poin_e'],
        ]);
    }
}
