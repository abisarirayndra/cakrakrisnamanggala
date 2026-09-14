<?php

namespace App\Support;

use DateTimeInterface;

final class AbsensiStatus
{
    public const TELAT = 0;

    public const ONTIME = 1;

    public const HADIR = 1;

    public const IZIN = 2;

    public const SAKIT = 3;

    public const ALPA = 4;

    public static function dariDatang(DateTimeInterface $datang, DateTimeInterface $mulai): int
    {
        return $datang > $mulai ? self::TELAT : self::ONTIME;
    }

    public static function label(int $status): string
    {
        return match ($status) {
            self::TELAT => 'Telat',
            self::ONTIME => 'Ontime',
            self::IZIN => 'Izin',
            self::SAKIT => 'Sakit',
            self::ALPA => 'Alpa',
            default => '—',
        };
    }

    public static function tampilkan(int $status, ?DateTimeInterface $datang = null, ?DateTimeInterface $mulai = null): string
    {
        if (in_array($status, [self::IZIN, self::SAKIT, self::ALPA], true)) {
            return self::label($status);
        }

        if ($datang && $mulai) {
            return self::label(self::dariDatang($datang, $mulai));
        }

        return self::label($status);
    }

    public static function warnaTampil(int $status, ?DateTimeInterface $datang = null, ?DateTimeInterface $mulai = null): ?string
    {
        if (in_array($status, [self::IZIN, self::SAKIT, self::ALPA], true)) {
            return null;
        }

        if ($datang && $mulai) {
            return self::warna(self::dariDatang($datang, $mulai));
        }

        return self::warna($status);
    }

    public static function warna(int $status): ?string
    {
        return match ($status) {
            self::TELAT => 'var(--ck-danger)',
            self::ONTIME => 'var(--ck-success)',
            default => null,
        };
    }
}
