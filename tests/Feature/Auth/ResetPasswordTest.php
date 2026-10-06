<?php

namespace Tests\Feature\Auth;

use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesPendaftaranSchema;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
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

    public function test_reset_page_uses_guest_cakra_layout(): void
    {
        $this->get(route('reset'))
            ->assertOk()
            ->assertSee('ck-login-card', false)
            ->assertSee('Lupa password')
            ->assertDontSee('bg-gradient-warning', false);
    }

    public function test_wrong_token_shows_error_on_reset_page(): void
    {
        User::factory()->create(['email' => 'a@example.com', 'nomor_registrasi' => 'REG-1']);

        $this->from(route('reset'))
            ->post(route('submit_email'), ['email' => 'a@example.com', 'token' => 'SALAH'])
            ->assertRedirect(route('reset'));

        $this->get(route('reset'))->assertSee('Token salah');
    }

    public function test_valid_token_opens_new_password_form(): void
    {
        User::factory()->create(['email' => 'a@example.com', 'nomor_registrasi' => 'REG-1']);

        $this->post(route('submit_email'), ['email' => 'a@example.com', 'token' => 'REG-1'])
            ->assertRedirect(route('form_reset'));

        $this->get(route('form_reset'))
            ->assertOk()
            ->assertSee('ck-login-card', false)
            ->assertSee('Password baru')
            ->assertDontSee('bg-gradient-warning', false);
    }

    public function test_form_reset_without_token_goes_back_to_reset(): void
    {
        $this->get(route('form_reset'))->assertRedirect(route('reset'));
    }

    public function test_mismatched_confirmation_is_rejected(): void
    {
        $user = User::factory()->create(['token_reset' => 'tok123', 'password' => Hash::make('lama123')]);

        $this->from(route('form_reset'))
            ->post(route('upreset'), [
                'token' => 'tok123',
                'password' => 'baru123',
                'password_confirmation' => 'beda123',
            ])
            ->assertRedirect(route('form_reset'))
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('lama123', $user->fresh()->password));
    }

    public function test_successful_reset_updates_password_and_shows_status_on_login(): void
    {
        $user = User::factory()->create(['token_reset' => 'tok123']);

        $this->post(route('upreset'), [
            'token' => 'tok123',
            'password' => 'baru123',
            'password_confirmation' => 'baru123',
        ])->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('baru123', $user->password));
        $this->assertNull($user->token_reset);

        $this->get(route('login'))->assertSee('Password berhasil diperbarui');
    }

    public function test_unknown_token_redirects_to_reset_with_error(): void
    {
        $this->post(route('upreset'), [
            'token' => 'tidakada',
            'password' => 'baru123',
            'password_confirmation' => 'baru123',
        ])->assertRedirect(route('reset'));

        $this->get(route('reset'))->assertSee('Link reset tidak valid');
    }
}
