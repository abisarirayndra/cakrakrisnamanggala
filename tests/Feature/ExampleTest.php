<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_login_page_returns_ok(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
