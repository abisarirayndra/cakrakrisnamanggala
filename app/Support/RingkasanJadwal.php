<?php

namespace App\Support;

use App\AbsensiPelajar;
use App\AbsensiPendidik;
use App\Jadwal;
use Illuminate\Support\Collection;

final class RingkasanJadwal
{
    /**
     * @return array{
     *     datangPendidik: Collection,
     *     datangPelajar: Collection,
     *     izinPendidik: Collection,
     *     izinPelajar: Collection,
     *     jurnal: ?string
     * }
     */
    public static function buat(Jadwal $slot): array
    {
        $hadirPendidik = AbsensiPendidik::query()
            ->where('jadwal_id', $slot->id)
            ->with('pendidik')
            ->orderBy('id')
            ->get();
        $hadirPelajar = AbsensiPelajar::query()
            ->where('jadwal_id', $slot->id)
            ->with('pelajar')
            ->orderBy('id')
            ->get();

        [$datangPendidik, $izinPendidik] = self::pisahDatangIzin($hadirPendidik);
        [$datangPelajar, $izinPelajar] = self::pisahDatangIzin($hadirPelajar);

        $jurnalUtama = $hadirPendidik->first(
            fn ($row) => (int) $row->pendidik_id === (int) $slot->pendidik_id && filled($row->jurnal)
        );
        $jurnalLain = $hadirPendidik->first(fn ($row) => filled($row->jurnal));

        return [
            'datangPendidik' => $datangPendidik,
            'datangPelajar' => $datangPelajar,
            'izinPendidik' => $izinPendidik,
            'izinPelajar' => $izinPelajar,
            'jurnal' => $jurnalUtama?->jurnal ?? $jurnalLain?->jurnal,
        ];
    }

    private static function pisahDatangIzin(Collection $rows): array
    {
        $izinStatus = [AbsensiStatus::IZIN, AbsensiStatus::SAKIT, AbsensiStatus::ALPA];

        return [
            $rows->filter(fn ($row) => $row->datang !== null)->values(),
            $rows->filter(fn ($row) => $row->datang === null && in_array((int) $row->status, $izinStatus, true))->values(),
        ];
    }
}
