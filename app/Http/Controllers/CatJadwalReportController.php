<?php

namespace App\Http\Controllers;

use App\CatJadwal;
use App\Support\AdminVisibility;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CatJadwalReportController extends Controller
{
    public function pdf(CatJadwal $jadwal): Response
    {
        $actor = auth()->user();
        abort_unless($actor?->isAdmin(), 403);
        abort_unless(AdminVisibility::catJadwalQuery($actor)->whereKey($jadwal->id)->exists(), 403);

        $jadwal->load(['banks.soal.opsi', 'sesi.pelajar.kelas', 'sesi.jawaban']);

        $logoPath = public_path('img/krisna.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $namaBerkas = 'laporan-paket-'.Str::slug($jadwal->nama ?: 'tes').'.pdf';

        return Pdf::loadView('admin.cat.jadwal-report-pdf', [
            'logo' => $logo,
            'jadwal' => $jadwal,
            'baris' => $jadwal->ringkasanPelajar(),
            'pencetak' => $actor->nama,
        ])->setPaper('a4')->download($namaBerkas);
    }
}
