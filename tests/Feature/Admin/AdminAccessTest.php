<?php

namespace Tests\Feature\Admin;

use App\Markas;
use App\User;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use CreatesAdminMasterSchema;

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
        $this->setUpAdminMasterSchema();
    }

    public function test_non_super_admin_is_forbidden_on_cat_paket(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.dinas.paket'))
            ->assertForbidden();
    }

    public function test_super_admin_can_open_cat_paket_route(): void
    {
        $response = $this->actingAs($this->superAdmin())
            ->get(route('admin.dinas.paket'));

        $this->assertNotSame(403, $response->status());
    }

    public function test_non_admin_is_forbidden_on_admin_beranda(): void
    {
        $pelajar = User::factory()->create(['role_id' => 4]);

        $this->actingAs($pelajar)
            ->get(route('admin.beranda'))
            ->assertForbidden();
    }

    public function test_staf_admin_beranda_redirects_into_admin_panel(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get('/staf-admin/beranda')
            ->assertRedirect(route('admin.beranda'));
    }

    public function test_beranda_hides_admin_and_cat_from_non_super(): void
    {
        $this->actingAs($this->markasAdmin())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Pelajar')
            ->assertSee('Pendidik')
            ->assertDontSee('data-nav="admin"', false)
            ->assertDontSee('data-nav="cat"', false);
    }

    public function test_beranda_shows_admin_and_cat_for_super(): void
    {
        $this->actingAs($this->superAdmin())
            ->get(route('admin.beranda'))
            ->assertOk()
            ->assertSee('Superadmin')
            ->assertSee('data-nav="admin"', false)
            ->assertSee('data-nav="cat"', false);
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => true,
        ]);
    }

    private function markasAdmin(): User
    {
        $markas = Markas::create(['markas' => 'Genteng']);
        $user = User::factory()->create([
            'role_id' => 2,
            'is_super_admin' => false,
        ]);
        $user->markas()->attach($markas->id);

        return $user;
    }
}
