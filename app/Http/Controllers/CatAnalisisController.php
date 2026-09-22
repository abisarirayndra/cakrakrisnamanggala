<?php

namespace App\Http\Controllers;

use App\CatJadwal;
use App\Support\CatAnalisisPaket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CatAnalisisController extends Controller
{
    public function pdf(CatJadwal $jadwal): Response
    {
        $pendidik = $this->pendidikBoleh($jadwal);
        $detail = CatAnalisisPaket::detail($jadwal, (int) $pendidik->id);

        return $this->unduhPdf('pendidik.cat.analisis-siswa-pdf', 'analisis-siswa-'.Str::slug($jadwal->nama ?: 'tes').'.pdf', [
            'jadwal' => $detail['jadwal'],
            'banks' => $detail['banks'],
            'siswa' => $detail['siswa'],
            'pencetak' => $pendidik->nama,
        ]);
    }

    public function pdfSoal(CatJadwal $jadwal): Response
    {
        $pendidik = $this->pendidikBoleh($jadwal);
        $detail = CatAnalisisPaket::detail($jadwal, (int) $pendidik->id);

        return $this->unduhPdf('pendidik.cat.analisis-soal-pdf', 'analisis-soal-'.Str::slug($jadwal->nama ?: 'tes').'.pdf', [
            'jadwal' => $detail['jadwal'],
            'banks' => $detail['banks'],
            'soal' => $detail['soal'],
            'ringkasan' => $detail['ringkasan'],
            'pencetak' => $pendidik->nama,
        ]);
    }

    private function pendidikBoleh(CatJadwal $jadwal)
    {
        $pendidik = auth()->user();
        abort_unless($pendidik?->isPengajar(), 403);
        abort_unless(CatAnalisisPaket::milikPendidik($jadwal, (int) $pendidik->id), 403);

        return $pendidik;
    }

    private function unduhPdf(string $view, string $namaBerkas, array $data): Response
    {
        $logoPath = public_path('img/krisna.png');
        $data['logo'] = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        return Pdf::loadView($view, $data)->setPaper('a4')->download($namaBerkas);
    }
}
