<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class SiswaExport implements FromView
{
    use Exportable;

    public function view(): View
    {
        return view('super.pengguna.pelajar.cetak-pelajar', [
            'pelajar' => User::join('kelas','kelas.id','=','users.kelas_id')
            ->join('adm_pelajars','adm_pelajars.pelajar_id','=','users.id')
            ->join('adm_markas','adm_markas.id','=','adm_pelajars.markas_id')
            ->select('users.id','users.nama','kelas.nama as kelas'
                    ,'adm_pelajars.tempat_lahir','adm_pelajars.tanggal_lahir','adm_pelajars.alamat','adm_pelajars.nik',
                    'adm_pelajars.nisn','adm_pelajars.sekolah','adm_pelajars.wali','adm_pelajars.wa_wali','adm_pelajars.wa',
                    'adm_pelajars.ibu','adm_pelajars.foto','adm_markas.markas')
            ->where('role_id', 4)
            ->orderBy('users.updated_at', 'desc')
            ->get()
        ]);
    }
}
