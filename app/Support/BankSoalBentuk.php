<?php

namespace App\Support;

final class BankSoalBentuk
{
    public const MATEMATIS = 'matematis';

    public const BIASA = 'biasa';

    public static function label(string $bentuk): string
    {
        return match ($bentuk) {
            self::MATEMATIS => 'Matematis',
            self::BIASA => 'Biasa',
            default => $bentuk,
        };
    }
}
