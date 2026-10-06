<?php

namespace Tests\Feature\Support;

use App\Support\Foto;
use Tests\TestCase;

class FotoTest extends TestCase
{
    public function test_default_image_exists(): void
    {
        $this->assertFileExists(public_path(Foto::DEFAULT));
    }

    public function test_empty_file_uses_default(): void
    {
        $this->assertSame(asset(Foto::DEFAULT), Foto::url('img/pelajar', null));
        $this->assertSame(asset(Foto::DEFAULT), Foto::url('img/pelajar', ''));
        $this->assertSame(public_path(Foto::DEFAULT), Foto::path('pendidik/img', null));
    }

    public function test_missing_file_uses_default(): void
    {
        $this->assertSame(asset(Foto::DEFAULT), Foto::url('img/pelajar', 'tidak-ada-'.uniqid().'.jpg'));
        $this->assertSame(public_path(Foto::DEFAULT), Foto::path('pendidik/img', 'tidak-ada.jpg'));
    }

    public function test_existing_file_is_used(): void
    {
        $this->assertSame(asset('img/krisna.png'), Foto::url('img', 'krisna.png'));
        $this->assertSame(public_path('img/krisna.png'), Foto::path('/img/', 'krisna.png'));
    }
}
