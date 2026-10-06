<?php

namespace App\Support;

class Foto
{
    public const DEFAULT = 'img/default-foto.png';

    public static function url(string $folder, ?string $file): string
    {
        return asset(self::relatif($folder, $file));
    }

    public static function path(string $folder, ?string $file): string
    {
        return public_path(self::relatif($folder, $file));
    }

    private static function relatif(string $folder, ?string $file): string
    {
        $file = trim((string) $file);

        if ($file === '') {
            return self::DEFAULT;
        }

        $relatif = trim($folder, '/').'/'.ltrim($file, '/');

        return is_file(public_path($relatif)) ? $relatif : self::DEFAULT;
    }
}
