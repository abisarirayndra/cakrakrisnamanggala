<?php

namespace App\Support;

final class BankSoalTipe
{
    public const TUNGGAL = 'tunggal';

    public const PEMBOBOTAN = 'pembobotan';

    public static function label(string $tipe): string
    {
        return match ($tipe) {
            self::TUNGGAL => 'Jawaban Tunggal',
            self::PEMBOBOTAN => 'Pembobotan',
            default => $tipe,
        };
    }
}
