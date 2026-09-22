<?php

namespace App\Http\Controllers;

use App\CatJadwal;
use App\CatSesi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CatTesReportController extends Controller
{
    public function pdf(CatJadwal $jadwal): Response
    {
        $pelajar = auth()->user();
        abort_unless($pelajar?->isPelajar(), 403);

        $jadwal->load(['banks.soal.opsi', 'sesi.pelajar.kelas', 'sesi.jawaban']);

        $punyaHasil = $jadwal->sesi
            ->where('pelajar_id', $pelajar->id)
            ->contains(fn (CatSesi $sesi) => $sesi->sudahSelesai());

        abort_unless($punyaHasil, 403);

        $logoPath = public_path('img/krisna.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $namaBerkas = 'laporan-cat-'.Str::slug($jadwal->nama ?: 'tes').'.pdf';

        return Pdf::loadView('pelajar.cat.tes-pdf', [
            'logo' => $logo,
            'jadwal' => $jadwal,
            'pelajar' => $pelajar->loadMissing('kelas'),
            'ringkasan' => $jadwal->ringkasanUntuk((int) $pelajar->id),
        ])->setPaper('a4')->download($namaBerkas);
    }
}
