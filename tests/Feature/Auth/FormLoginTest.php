<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\FormLogin;
use App\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\Concerns\CreatesPendaftaranSchema;
use Tests\TestCase;

class FormLoginTest extends TestCase
{
    use CreatesPendaftaranSchema;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPendaftaranSchema();
    }

    public function test_login_page_renders_livewire_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSeeLivewire(FormLogin::class)
            ->assertSee('Sistem E-Learning Terpadu');
    }

    public function test_unknown_email_shows_the_legacy_message(): void
    {
        Livewire::test(FormLogin::class)
            ->set('email', 'tidakada@example.com')
            ->set('password', 'secret123')
            ->call('masuk')
            ->assertHasErrors(['email'])
            ->assertSee('Login gagal, email tidak terdaftar');
    }

    public function test_wrong_password_shows_the_legacy_message(): void
    {
        User::factory()->create([
            'email' => 'pelajar@example.com',
            'password' => Hash::make('benar123'),
            'role_id' => 4,
        ]);

        Livewire::test(FormLogin::class)
            ->set('email', 'pelajar@example.com')
            ->set('password', 'salah')
            ->call('masuk')
            ->assertHasErrors(['password'])
            ->assertSee('Login gagal, password salah');
    }

    public function test_pelajar_is_redirected_to_beranda(): void
    {
        User::factory()->create([
            'email' => 'pelajar@example.com',
            'password' => Hash::make('benar123'),
            'role_id' => 4,
        ]);

        Livewire::test(FormLogin::class)
            ->set('email', 'pelajar@example.com')
            ->set('password', 'benar123')
            ->call('masuk')
            ->assertHasNoErrors()
            ->assertRedirect(route('pelajar.dinas.beranda'));
    }

    public function test_super_admin_is_redirected_to_admin_beranda(): void
    {
        User::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => Hash::make('benar123'),
            'role_id' => 2,
            'is_super_admin' => true,
        ]);

        Livewire::test(FormLogin::class)
            ->set('email', 'superadmin@example.com')
            ->set('password', 'benar123')
            ->call('masuk')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.beranda'));
    }

    public function test_non_super_admin_is_redirected_to_admin_beranda(): void
    {
        User::factory()->create([
            'email' => 'staf@example.com',
            'password' => Hash::make('benar123'),
            'role_id' => 2,
            'is_super_admin' => false,
        ]);

        Livewire::test(FormLogin::class)
            ->set('email', 'staf@example.com')
            ->set('password', 'benar123')
            ->call('masuk')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.beranda'));
    }

    public function test_super_role_is_forbidden(): void
    {
        User::factory()->create([
            'email' => 'super@example.com',
            'password' => Hash::make('benar123'),
            'role_id' => 1,
        ]);

        Livewire::test(FormLogin::class)
            ->set('email', 'super@example.com')
            ->set('password', 'benar123')
            ->call('masuk')
            ->assertForbidden();
    }
}
