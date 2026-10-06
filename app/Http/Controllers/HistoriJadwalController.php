<?php

namespace App\Http\Controllers;

use App\Support\AdminVisibility;
use App\Support\RingkasanJadwal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class HistoriJadwalController extends Controller
{
    public function pdf(int $jadwal): Response
    {
        $actor = auth()->user();
        abort_unless($actor?->isAdmin(), 403);

        $slot = AdminVisibility::jadwalQuery($actor)
            ->with(['mapel', 'pendidik', 'kelas.markas'])
            ->where('adm_jadwal.id', $jadwal)
            ->firstOrFail();

        $ringkasan = RingkasanJadwal::buat($slot);
        $logoPath = public_path('img/krisna.png');
        $logo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $namaBerkas = collect([
            $slot->mulai->format('Y-m-d'),
            $slot->mapel?->mapel ?: 'mapel',
            $slot->kelas?->nama ?: 'kelas',
        ])->map(fn ($bagian) => trim(preg_replace('/[\\\\\/:*?"<>|]+/', ' ', $bagian)))
            ->implode('-').'.pdf';

        return Pdf::loadView('admin.jadwal.histori-pdf', [
            'logo' => $logo,
            'slot' => $slot,
            'jurnal' => $ringkasan['jurnal'],
            'datangPendidik' => $ringkasan['datangPendidik'],
            'datangPelajar' => $ringkasan['datangPelajar'],
            'izinPendidik' => $ringkasan['izinPendidik'],
            'izinPelajar' => $ringkasan['izinPelajar'],
            'pencetak' => $actor->nama,
        ])->setPaper('a4')->download($namaBerkas);
    }
}
