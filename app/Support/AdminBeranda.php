<?php

namespace App\Support;

use App\CatJadwal;
use App\User;

final class AdminBeranda
{
    public static function data(User $actor): array
    {
        $pendaftar = AdminVisibility::pendaftarQuery($actor)
            ->with(['pelajar.markas'])
            ->orderByDesc('users.id')
            ->limit(8)
            ->get();

        $pelajarPerKelas = AdminVisibility::pelajarQuery($actor)
            ->leftJoin('kelas as ck_kelas', 'ck_kelas.id', '=', 'users.kelas_id')
            ->select('ck_kelas.nama as kelas_nama')
            ->selectRaw('count(users.id) as jumlah')
            ->groupBy('ck_kelas.nama')
            ->orderBy('ck_kelas.nama')
            ->get()
            ->map(fn ($row) => [
                'nama' => $row->kelas_nama ?: 'Tanpa kelas',
                'jumlah' => (int) $row->jumlah,
            ]);

        $hariIni = now()->toDateString();
        $jadwalHariIni = AdminVisibility::jadwalQuery($actor)
            ->with(['mapel', 'pendidik', 'kelas'])
            ->whereDate('adm_jadwal.mulai', $hariIni)
            ->orderBy('adm_jadwal.mulai')
            ->get();

        $catHariIni = AdminVisibility::catJadwalQuery($actor)
            ->with('banks')
            ->where('mulai', '<=', now()->copy()->endOfDay())
            ->where('selesai', '>=', now()->copy()->startOfDay())
            ->orderBy('mulai')
            ->get()
            ->map(fn (CatJadwal $row) => self::barisCat($row));

        return [
            'jumlahPendaftar' => AdminVisibility::pendaftarQuery($actor)->count(),
            'pendaftar' => $pendaftar,
            'jumlahPelajar' => AdminVisibility::pelajarQuery($actor)->count(),
            'kelasPelajar' => $pelajarPerKelas,
            'jadwalHariIni' => $jadwalHariIni,
            'catHariIni' => $catHariIni,
        ];
    }

    private static function barisCat(CatJadwal $row): array
    {
        $now = now();
        $status = $now->lt($row->mulai)
            ? 'Belum mulai'
            : ($now->gte($row->selesai) ? 'Selesai' : 'Berlangsung');

        return [
            'id' => $row->id,
            'nama' => $row->nama,
            'waktu' => $row->labelWaktuPelaksanaan(),
            'token' => $row->token,
            'status' => $status,
            'banks' => $row->banks->pluck('nama')->filter()->values(),
        ];
    }
}
