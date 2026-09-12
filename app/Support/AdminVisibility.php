<?php

namespace App\Support;

use App\Jadwal;
use App\Kelas;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class AdminVisibility
{
    public static function pelajarQuery(User $actor, int $roleId = 4): Builder
    {
        $query = User::query()
            ->join('adm_pelajars', 'adm_pelajars.pelajar_id', '=', 'users.id')
            ->where('users.role_id', $roleId)
            ->select('users.*');

        if (! $actor->isSuperAdmin()) {
            $ids = $actor->markasIds();

            if ($ids === []) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('adm_pelajars.markas_id', $ids);
            }
        }

        return $query;
    }

    public static function pendaftarQuery(User $actor): Builder
    {
        $query = User::query()
            ->join('adm_pelajars', 'adm_pelajars.pelajar_id', '=', 'users.id')
            ->where('users.role_id', 5)
            ->select('users.*');

        if (! $actor->isSuperAdmin()) {
            $ids = $actor->markasIds();

            if ($ids === []) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('adm_pelajars.markas_id', $ids);
            }
        }

        return $query;
    }

    public static function pendidikQuery(User $actor): Builder
    {
        $query = User::query()
            ->join('adm_pendidik', 'adm_pendidik.pendidik_id', '=', 'users.id')
            ->where('users.role_id', 3)
            ->select('users.*');

        if (! $actor->isSuperAdmin()) {
            $ids = $actor->markasIds();

            if ($ids === []) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('adm_pendidik.markas_id', $ids);
            }
        }

        return $query;
    }

    public static function kelasForJadwal(User $actor): Builder
    {
        $query = Kelas::query()->whereNotNull('markas_id')->orderBy('nama');

        if (! $actor->isSuperAdmin()) {
            $ids = $actor->markasIds();
            $query = $ids === [] ? $query->whereRaw('1 = 0') : $query->whereIn('markas_id', $ids);
        }

        return $query;
    }

    public static function jadwalQuery(User $actor): Builder
    {
        $query = Jadwal::query()
            ->join('kelas', 'kelas.id', '=', 'adm_jadwal.kelas_id')
            ->select('adm_jadwal.*');

        if (! $actor->isSuperAdmin()) {
            $ids = $actor->markasIds();
            $query = $ids === [] ? $query->whereRaw('1 = 0') : $query->whereIn('kelas.markas_id', $ids);
        }

        return $query;
    }

    public static function pendidikForKelas(Kelas $kelas): Builder
    {
        return User::query()
            ->join('adm_pendidik', 'adm_pendidik.pendidik_id', '=', 'users.id')
            ->where('users.role_id', 3)
            ->where('adm_pendidik.markas_id', $kelas->markas_id)
            ->select('users.*')
            ->orderBy('users.nama');
    }
}
