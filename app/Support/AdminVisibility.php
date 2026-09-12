<?php

namespace App\Support;

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
}
