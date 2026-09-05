<?php

namespace Tests\Feature\Admin;

use App\Markas;
use App\Pelajar;
use App\Pendidik;
use App\Support\AdminVisibility;
use App\User;
use Tests\Concerns\CreatesAdminMasterSchema;
use Tests\TestCase;

class AdminIdentityTest extends TestCase
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

    public function test_is_super_admin_reads_the_flag_not_role_one(): void
    {
        $flag = User::factory()->create(['role_id' => 2, 'is_super_admin' => true]);
        $staf = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $legacy = User::factory()->create(['role_id' => 1, 'is_super_admin' => false]);

        $this->assertTrue($flag->isSuperAdmin());
        $this->assertFalse($staf->isSuperAdmin());
        $this->assertFalse($legacy->isSuperAdmin());
        $this->assertTrue($legacy->isSuper());
        $this->assertTrue($staf->isStafAdmin());
        $this->assertSame('admin.beranda', $flag->dashboardRouteName());
        $this->assertSame('admin.beranda', $staf->dashboardRouteName());
    }

    public function test_non_super_pelajar_query_is_limited_to_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);

        $super = User::factory()->create(['role_id' => 2, 'is_super_admin' => true]);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        $a = User::factory()->create(['role_id' => 4, 'nama' => 'Pelajar Genteng']);
        $b = User::factory()->create(['role_id' => 4, 'nama' => 'Pelajar Jember']);
        Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
        Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);

        $superIds = AdminVisibility::pelajarQuery($super)->pluck('users.id')->all();
        $adminIds = AdminVisibility::pelajarQuery($admin)->pluck('users.id')->all();

        $this->assertEqualsCanonicalizing([$a->id, $b->id], $superIds);
        $this->assertEquals([$a->id], $adminIds);
    }

    public function test_non_super_pendidik_query_is_limited_to_assigned_markas(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $jember = Markas::create(['markas' => 'Jember']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $admin->markas()->attach($genteng->id);

        $p1 = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Genteng']);
        $p2 = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Jember']);
        Pendidik::create(['pendidik_id' => $p1->id, 'mapel_id' => 10, 'markas_id' => $genteng->id]);
        Pendidik::create(['pendidik_id' => $p2->id, 'mapel_id' => 10, 'markas_id' => $jember->id]);

        $ids = AdminVisibility::pendidikQuery($admin)->pluck('users.id')->all();
        $this->assertEquals([$p1->id], $ids);
    }

    public function test_empty_markas_assignment_sees_no_pelajar(): void
    {
        $genteng = Markas::create(['markas' => 'Genteng']);
        $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
        $pelajar = User::factory()->create(['role_id' => 4]);
        Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

        $this->assertSame([], AdminVisibility::pelajarQuery($admin)->pluck('users.id')->all());
    }
}
