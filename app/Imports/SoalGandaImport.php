<?php

namespace App\Imports;

use App\SoalDinasGanda;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoalGandaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SoalDinasGanda([
            'dn_tes_id' => $row['kode_tes'],
            'nomor_soal' => $row['nomor_soal'],
            'soal' => $row['soal'],
            'opsi_a' => $row['opsi_a'],
            'opsi_b' => $row['opsi_b'],
            'opsi_c' => $row['opsi_c'],
            'opsi_d' => $row['opsi_d'],
            'opsi_e' => $row['opsi_e'],
            'kunci' => $row['kunci'],
        ]);
    }
}
