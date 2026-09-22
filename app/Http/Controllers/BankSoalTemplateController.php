<?php

namespace App\Http\Controllers;

use App\BankPaket;
use App\Exports\BankSoalTemplateExport;
use App\Support\BankSoalTipe;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BankSoalTemplateController extends Controller
{
    public function __invoke(BankPaket $paket): BinaryFileResponse
    {
        abort_unless(auth()->user()?->isPengajar(), 403);
        abort_unless((int) $paket->pendidik_id === (int) auth()->id(), 404);

        $nama = $paket->tipe === BankSoalTipe::PEMBOBOTAN
            ? 'template-soal-pembobotan.xlsx'
            : 'template-soal-tunggal.xlsx';

        return Excel::download(new BankSoalTemplateExport($paket->tipe), $nama);
    }
}
