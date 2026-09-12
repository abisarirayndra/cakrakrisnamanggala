# Admin Role Merge and Master Data Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Merge admin and staf-admin into `role_id` 2 with `is_super_admin`, scope master data by `admin_markas`, and ship Livewire master pages for admin, pelajar, and pendidik.

**Architecture:** Boolean flag on `users` plus pivot `admin_markas`. `User::isSuperAdmin()` reads the flag (not `role_id` 1). `App\Support\AdminVisibility` filters pelajar/pendidik queries. One `/admin` panel; CAT/dinas and master admin require `superadmin-role`. Livewire v3 lists replace SB Admin DataTables for the three masters.

**Tech Stack:** Laravel 12, PHP 8.3, Livewire v3, Alpine, Bootstrap 5.3, PHPUnit 11, SQLite in-memory for tests.

**Spec:** `docs/superpowers/specs/2026-09-05-admin-master-data-design.md`

## Global Constraints

- PHP 8.3 (`C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64\php.exe`); default PATH PHP is 7.4 — always prefix this binary.
- PHPUnit: `php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit …`
- Livewire v3 + Alpine + Bootstrap 5.3; Plus Jakarta Sans; cream `#F6F3EE`, navy `#243044`, gold `#B8954A`; no SB Admin warning yellow on these screens.
- Do not change `adm_pelajars` columns. Visibility is markas, not `created_by`.
- `User::isSuper()` stays `role_id === 1`. `User::isSuperAdmin()` is the flag.
- Default new pendidik password: `pendidik123`.
- Unauthorized super-only URLs: HTTP 403.
- Out of scope: absensi, jadwal, pendaftar approval modal, multi-markas UI beyond one select, reset-password restyle.

## PHPUnit helper (every test run)

```powershell
$env:PATH = "C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64;" + $env:PATH
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit <test-file>
```

## File map

- `database/migrations/2026_09_05_000001_add_is_super_admin_and_admin_markas.php` — column, pivot, data backfill
- `app/User.php` — flag, `markas()`, `markasIds()`, `dashboardRouteName()`, `isStafAdmin()` alias
- `app/Markas.php` — `admins()` belongsToMany
- `app/Pendidik.php` — `public const DEFAULT_PASSWORD = 'pendidik123'`
- `app/Support/AdminVisibility.php` — query filters
- `app/Http/Middleware/SuperAdminMiddleware.php` — flag 403
- `app/Http/Kernel.php` — alias `superadmin-role`
- `app/Http/Controllers/AuthController.php` — drop role 7 welcome branch
- `app/Livewire/Auth/FormLogin.php` — no logic change if `dashboardRouteName()` is correct
- `app/Http/Controllers/AdminController.php` — new beranda view
- `resources/views/layouts/panel-cakra.blade.php` — admin shell
- `resources/views/admin/beranda.blade.php` — simple landing
- `app/Livewire/Admin/MasterAdmin.php` + view
- `app/Livewire/Admin/MasterPelajar.php` + view
- `app/Livewire/Admin/MasterPendidik.php` + view
- `routes/web.php` — Livewire masters, wrap CAT with `superadmin-role`, staf-admin redirects
- `public/css/cakra-admin.css` — `.ck-shell-wide`
- `tests/Concerns/CreatesAdminMasterSchema.php`
- `tests/Feature/Admin/AdminIdentityTest.php`
- `tests/Feature/Admin/AdminAccessTest.php`
- `tests/Feature/Admin/MasterAdminTest.php`
- `tests/Feature/Admin/MasterPelajarTest.php`
- `tests/Feature/Admin/MasterPendidikTest.php`
- `tests/Feature/Auth/FormLoginTest.php` — add two admin login cases

---

### Task 1: Schema, User identity, markas visibility

**Files:**
- Create: `database/migrations/2026_09_05_000001_add_is_super_admin_and_admin_markas.php`
- Create: `app/Support/AdminVisibility.php`
- Create: `tests/Concerns/CreatesAdminMasterSchema.php`
- Create: `tests/Feature/Admin/AdminIdentityTest.php`
- Modify: `app/User.php`
- Modify: `app/Markas.php`
- Modify: `app/Pendidik.php`

**Interfaces:**
- Consumes: existing `users`, `adm_markas`, `adm_pelajars`, `adm_pendidik`
- Produces:
  - `User::isSuperAdmin(): bool` — `(bool) $this->is_super_admin`
  - `User::markas(): BelongsToMany` — `belongsToMany(Markas::class, 'admin_markas', 'user_id', 'markas_id')`
  - `User::markasIds(): array` — `array_map('intval', $this->markas()->pluck('adm_markas.id')->all())`
  - `User::isStafAdmin(): bool` — `return $this->isAdmin();`
  - `User::dashboardRouteName(): ?string` — `2` and leftover `7` → `'admin.beranda'`
  - `User::isSuper(): bool` unchanged (`role_id === 1`)
  - `AdminVisibility::pelajarQuery(User $actor): \Illuminate\Database\Eloquent\Builder`
  - `AdminVisibility::pendidikQuery(User $actor): \Illuminate\Database\Eloquent\Builder`
  - `Pendidik::DEFAULT_PASSWORD` = `'pendidik123'`

- [ ] **Step 1: Write the failing test file and schema trait**

`tests/Concerns/CreatesAdminMasterSchema.php`:

```php
<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesAdminMasterSchema
{
    protected function setUpAdminMasterSchema(): void
    {
        Schema::dropIfExists('admin_markas');
        Schema::dropIfExists('adm_pelajars');
        Schema::dropIfExists('adm_pendidik');
        Schema::dropIfExists('adm_markas');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama');
            $table->string('nomor_registrasi')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedInteger('role_id')->nullable();
            $table->boolean('is_super_admin')->default(false);
            $table->string('whatsapp')->nullable();
            $table->unsignedInteger('kelas_id')->nullable();
            $table->string('token_reset')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('adm_markas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('markas');
            $table->timestamps();
        });

        Schema::create('admin_markas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('markas_id');
            $table->timestamps();
            $table->unique(['user_id', 'markas_id']);
        });

        Schema::create('adm_pelajars', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pelajar_id');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('sekolah')->nullable();
            $table->integer('status_sekolah')->nullable();
            $table->string('wa')->nullable();
            $table->string('wali')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedInteger('markas_id')->nullable();
            $table->string('nik')->nullable();
            $table->string('nisn')->nullable();
            $table->string('ibu')->nullable();
            $table->string('wa_wali')->nullable();
            $table->timestamps();
        });

        Schema::create('adm_pendidik', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pendidik_id');
            $table->unsignedInteger('mapel_id')->nullable();
            $table->unsignedInteger('markas_id')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik')->nullable();
            $table->string('nip')->nullable();
            $table->string('alamat')->nullable();
            $table->string('wa')->nullable();
            $table->string('ibu')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }
}
```

`tests/Feature/Admin/AdminIdentityTest.php` — include `defineEnvironment` sqlite `:memory:` identical to `tests/Feature/Auth/FormLoginTest.php`. Tests:

```php
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
```

`AdminVisibility::pelajarQuery` / `pendidikQuery` must `join` `users` and return a `User` query (`User::query()->join(...)`) selecting `users.*` so `pluck('users.id')` works. Pelajar with `markas_id` null must not appear for non-super (join `whereIn` excludes null). Super sees them.

- [ ] **Step 2: Run tests, confirm they fail**

Run: PHPUnit `tests/Feature/Admin/AdminIdentityTest.php`

Expected: FAIL (`isSuperAdmin` / `AdminVisibility` missing, or `is_super_admin` unknown).

- [ ] **Step 3: Implement migration, models, helper**

Migration `up()`:

1. `$table->boolean('is_super_admin')->default(false)->after('role_id');` on `users` (guard with `Schema::hasColumn`).
2. Create `admin_markas` with `user_id`, `markas_id`, unique pair, timestamps. Skip FK if you follow other app migrations that sometimes omit them; prefer unsignedInteger matching `users.id` / `adm_markas.id`.
3. `DB::table('users')->where('role_id', 2)->update(['is_super_admin' => true]);`
4. For each `users.role_id = 7`: read `adm_pendidik.markas_id`; if not null, insert `admin_markas`. Then `DB::table('users')->where('role_id', 7)->update(['role_id' => 2, 'is_super_admin' => false]);`

`down()`: drop `admin_markas`, drop column `is_super_admin`.

`User`: add `'is_super_admin'` to `$fillable`; cast `'is_super_admin' => 'boolean'`. Replace `isStafAdmin()` body with `return $this->isAdmin();`. Add `isSuperAdmin()`, `markas()`, `markasIds()`. `dashboardRouteName()` match: `2, 7 => 'admin.beranda'`.

`Markas`: add `$fillable = ['markas'];` and `admins(): BelongsToMany`.

`Pendidik`: `public const DEFAULT_PASSWORD = 'pendidik123';`

`AdminVisibility`:

```php
public static function pelajarQuery(User $actor): Builder
{
    $query = User::query()
        ->join('adm_pelajars', 'adm_pelajars.pelajar_id', '=', 'users.id')
        ->where('users.role_id', 4)
        ->select('users.*');

    if (! $actor->isSuperAdmin()) {
        $ids = $actor->markasIds();
        if ($ids === []) {
            $query->whereRaw('1 = 0');
        } else {
            $query->whereIn('adm_pelajars.markas_id', $ids);
        }
    }

    return $query;
}
```

Same pattern for `pendidikQuery` joining `adm_pendidik` on `pendidik_id`, `role_id` 3, filter `adm_pendidik.markas_id`.

- [ ] **Step 4: Re-run tests, confirm pass**

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/2026_09_05_000001_add_is_super_admin_and_admin_markas.php app/User.php app/Markas.php app/Pendidik.php app/Support/AdminVisibility.php tests/Concerns/CreatesAdminMasterSchema.php tests/Feature/Admin/AdminIdentityTest.php
git commit -m "Add admin super flag and markas visibility."
```

---

### Task 2: Login both admin types to `admin.beranda`

**Files:**
- Modify: `tests/Feature/Auth/FormLoginTest.php`
- Modify: `tests/Concerns/CreatesPendaftaranSchema.php` — add `is_super_admin` boolean default false on `users` so login tests still boot
- Modify: `app/Http/Controllers/AuthController.php` — treat role 2 welcome for all admins; remove role 7 branch (after migration nobody is 7; leftover 7 still hits `dashboardRouteName`)

**Interfaces:**
- Consumes: `User::dashboardRouteName()`
- Produces: Livewire and POST `/log` send `role_id` 2 (flag on or off) to `admin.beranda`

- [ ] **Step 1: Write failing login tests**

In `FormLoginTest` (schema must include `is_super_admin` — update `CreatesPendaftaranSchema` users table in this same step before running, otherwise factory insert fails):

```php
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
```

If Task 1 already changed `dashboardRouteName`, these may pass immediately — that is acceptable. If `CreatesPendaftaranSchema` lacks the column, they error until the trait is updated.

- [ ] **Step 2: Run `tests/Feature/Auth/FormLoginTest.php`**

Expected: FAIL only if schema/column missing or redirect still `staf-admin.beranda`.

- [ ] **Step 3: Patch schema trait + AuthController welcome**

Add `$table->boolean('is_super_admin')->default(false);` to `CreatesPendaftaranSchema` users.

AuthController: `if ($user->role_id == 2) { Alert::success('Selamat datang', $user->isSuperAdmin() ? 'Admin' : 'Admin'); }` and delete the `role_id == 7` elseif. Leftover 7 still redirects via `dashboardRouteName`.

- [ ] **Step 4: Re-run FormLoginTest + AdminIdentityTest**

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/Auth/FormLoginTest.php tests/Concerns/CreatesPendaftaranSchema.php app/Http/Controllers/AuthController.php
git commit -m "Send every admin login to the shared beranda."
```

---

### Task 3: Superadmin middleware, CAT 403, staf-admin redirects

**Files:**
- Create: `app/Http/Middleware/SuperAdminMiddleware.php`
- Create: `tests/Feature/Admin/AdminAccessTest.php`
- Modify: `app/Http/Kernel.php` — `'superadmin-role' => \App\Http\Middleware\SuperAdminMiddleware::class`
- Modify: `app/Http/Middleware/AdminMiddleware.php` — `abort(403)` instead of `redirect()->back()` when not `isAdmin()`
- Modify: `routes/web.php`

**Interfaces:**
- Consumes: `User::isAdmin()`, `User::isSuperAdmin()`
- Produces: alias `superadmin-role`; CAT/dinas + old Highcharts administrasi + old pendaftar/suspend/staf controller routes require it; `/staf-admin/{any?}` auth redirect

- [ ] **Step 1: Write `AdminAccessTest`**

Use `CreatesAdminMasterSchema`. Helpers to make users:

```php
private function superAdmin(): User
{
    return User::factory()->create(['role_id' => 2, 'is_super_admin' => true]);
}

private function markasAdmin(): User
{
    $markas = Markas::create(['markas' => 'Genteng']);
    $user = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $user->markas()->attach($markas->id);
    return $user;
}
```

Tests (routes `admin.pengguna.admin` / `admin.pengguna.pelajar` may not exist yet — **do not** assert those until Tasks 5–6; this task only needs existing `admin.dinas.paket`, `admin.beranda`, `super.administrasi`, and staf prefix):

```php
public function test_non_super_admin_is_forbidden_on_cat_paket(): void
{
    $this->actingAs($this->markasAdmin())
        ->get(route('admin.dinas.paket'))
        ->assertForbidden();
}

public function test_super_admin_can_open_cat_paket_route(): void
{
    $this->actingAs($this->superAdmin())
        ->get(route('admin.dinas.paket'))
        ->assertOk();
}

public function test_non_admin_is_forbidden_on_admin_beranda(): void
{
    $pelajar = User::factory()->create(['role_id' => 4]);
    $this->actingAs($pelajar)->get(route('admin.beranda'))->assertForbidden();
}

public function test_staf_admin_beranda_redirects_into_admin_panel(): void
{
    $this->actingAs($this->markasAdmin())
        ->get('/staf-admin/beranda')
        ->assertRedirect(route('admin.beranda'));
}
```

`assertOk` on CAT paket may fail if the old controller queries missing tables — if so, change the super test to `assertStatus` not 403 (e.g. `assertNotForbidden()` / `assertStatus` not 403). Prefer `assertForbidden` vs not. Super hitting paket with empty sqlite may 500; then assert `assertStatus(500)` is wrong. Safer super test: only `assertNotEquals(403, $response->status())` after acting as super. Use:

```php
$this->actingAs($this->superAdmin())
    ->get(route('admin.dinas.paket'))
    ->assertStatus(403); // this must FAIL in step 2 if middleware not applied to non-super only
```

Wait — for super we want NOT 403. If controller 500s, still proves middleware allowed the request. Use:

```php
$response = $this->actingAs($this->superAdmin())->get(route('admin.dinas.paket'));
$this->assertNotSame(403, $response->status());
```

- [ ] **Step 2: Run `tests/Feature/Admin/AdminAccessTest.php`**

Expected: FAIL (non-super currently 200 on CAT; staf URL still 200/middleware bounce).

- [ ] **Step 3: Implement middleware and routes**

`SuperAdminMiddleware`:

```php
if (! $user || ! $user->isAdmin() || ! $user->isSuperAdmin()) {
    abort(403);
}
return $next($request);
```

`AdminMiddleware`: `abort(403)` if `!$user || !$user->isAdmin()`.

In `routes/web.php` inside the existing `admin` group:

Wrap every CAT/dinas/arsip/soal route **and** `super.administrasi`, pendaftar, suspended, and old `pengguna-staf-admin` controller routes in `Route::middleware('superadmin-role')->group(function () { ... });`.

Leave `admin.beranda` **outside** that inner group (all admins).

Replace the `staf-admin` route group with:

```php
Route::middleware('auth')->prefix('staf-admin')->group(function () {
    Route::get('/pengguna-pelajar', fn () => redirect()->route('admin.pengguna.pelajar'));
    Route::get('/{any?}', fn () => redirect()->route('admin.beranda'))->where('any', '.*');
});
```

The pelajar redirect will 500/invalid until Task 6 registers `admin.pengguna.pelajar`. **For this task only**, point that line at `admin.beranda` as well, then Task 6 changes it to the Livewire name. Spec allows unmatched → beranda. Do **not** reference `admin.pengguna.pelajar` until Task 6.

So this task: entire `/staf-admin/{any?}` → `admin.beranda`.

- [ ] **Step 4: Re-run AdminAccessTest + previous suites**

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Middleware/SuperAdminMiddleware.php app/Http/Kernel.php app/Http/Middleware/AdminMiddleware.php routes/web.php tests/Feature/Admin/AdminAccessTest.php
git commit -m "Gate CAT to superadmin and retire the staf-admin prefix."
```

---

### Task 4: Panel layout and beranda

**Files:**
- Create: `resources/views/layouts/panel-cakra.blade.php`
- Modify: `resources/views/admin/beranda.blade.php`
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `public/css/cakra-admin.css`
- Modify: `tests/Feature/Admin/AdminAccessTest.php` — add beranda menu assertions

**Interfaces:**
- Consumes: `auth()->user()->isSuperAdmin()`, `auth()->user()->nama`
- Produces: layout `layouts.panel-cakra` for Livewire `#[Layout('layouts.panel-cakra')]`; nav links listed below

Nav links (use these exact `route()` names even if masters are stubbed later — **register placeholder GET views in this task** so beranda tests can `assertSee` hrefs):

- `admin.beranda` — Beranda
- `admin.pengguna.admin` — Admin (super only)
- `admin.pengguna.pelajar` — Pelajar
- `admin.pengguna.pendidik` — Pendidik
- `admin.dinas.paket` — CAT (super only)

- [ ] **Step 1: Write beranda assertions in `AdminAccessTest`**

First add **temporary** routes in `web.php` inside admin group (not superadmin-only except admin master):

```php
Route::get('/pengguna-admin', fn () => abort(403))->middleware('superadmin-role')->name('admin.pengguna.admin');
Route::view('/pengguna-pelajar', 'admin.beranda')->name('admin.pengguna.pelajar');
Route::view('/pengguna-pendidik', 'admin.beranda')->name('admin.pengguna.pendidik');
```

Tasks 5–7 replace the closures/views with Livewire classes (same names). If you register them here, do not duplicate names later — swap the action.

Tests:

```php
public function test_beranda_hides_admin_and_cat_from_non_super(): void
{
    $this->actingAs($this->markasAdmin())
        ->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('Pelajar')
        ->assertSee('Pendidik')
        ->assertDontSee('href="'.route('admin.pengguna.admin').'"', false)
        ->assertDontSee('href="'.route('admin.dinas.paket').'"', false);
}

public function test_beranda_shows_admin_and_cat_for_super(): void
{
    $this->actingAs($this->superAdmin())
        ->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('Superadmin')
        ->assertSee(route('admin.pengguna.admin'), false)
        ->assertSee(route('admin.dinas.paket'), false);
}
```

`assertDontSee` on full href is brittle if Livewire escapes. Prefer `assertDontSee('>Admin</a>', false)` vs `assertSee('>Admin</a>', false)` with the nav label **Admin** only rendered for super. Non-super page still contains word "Admin" in "Cakra…" — use a distinctive `data-nav="admin"` attribute in the layout.

Layout snippet:

```blade
@if (auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.pengguna.admin') }}" data-nav="admin" class="small fw-semibold">Admin</a>
    <a href="{{ route('admin.dinas.paket') }}" data-nav="cat" class="small fw-semibold">CAT</a>
@endif
```

Tests: `assertSee('data-nav="admin"', false)` / `assertDontSee('data-nav="admin"', false)`.

- [ ] **Step 2: Run AdminAccessTest, confirm new tests fail**

Expected: FAIL (old SB Admin beranda, no `data-nav`).

- [ ] **Step 3: Implement layout, CSS, beranda, controller**

`panel-cakra.blade.php`: copy `guest-cakra` head (fonts, bootstrap 5.3, bootstrap-icons, `cakra-admin.css`, `@livewireStyles`). Body `ck-body`. Topbar navy: brand → `route('admin.beranda')`, links Beranda, Pelajar, Pendidik, conditional Admin+CAT, then nama + `route('logout')`. Main: `container ck-shell-wide`. Slot/yield like guest layout. `@livewireScripts`.

CSS:

```css
.ck-shell-wide {
    max-width: 1120px;
}
```

`AdminController::index`: `return view('admin.beranda');` (layout via `@extends('layouts.panel-cakra')` on the view). Do not query `Pendidik` (superadmin may not have `adm_pendidik`).

Beranda card: heading with `auth()->user()->nama`, subtitle `auth()->user()->isSuperAdmin() ? 'Superadmin' : 'Admin'`. Short links to Pelajar/Pendidik; super also Admin + CAT.

Remove Highcharts/administrasi tile from this new beranda.

- [ ] **Step 4: Re-run AdminAccessTest**

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/layouts/panel-cakra.blade.php resources/views/admin/beranda.blade.php app/Http/Controllers/AdminController.php public/css/cakra-admin.css routes/web.php tests/Feature/Admin/AdminAccessTest.php
git commit -m "Give admins a shared cream-and-navy panel shell."
```

---

### Task 5: Livewire Master Admin (super only)

**Files:**
- Create: `app/Livewire/Admin/MasterAdmin.php`
- Create: `resources/views/livewire/admin/master-admin.blade.php`
- Create: `tests/Feature/Admin/MasterAdminTest.php`
- Modify: `routes/web.php` — replace placeholder `admin.pengguna.admin` with `MasterAdmin::class` + `middleware('superadmin-role')`

**Interfaces:**
- Consumes: `User`, `Markas`, `isSuperAdmin()`, `markas()->attach`
- Produces:
  - `public string $cari = ''`
  - `public string $nama = ''`
  - `public string $email = ''`
  - `public string $password = ''`
  - `public bool $is_super_admin = false`
  - `public $markas_id = ''`
  - `public ?int $lihatId = null`
  - `tambah(): void`
  - `hapus(int $id): void`
  - `lihat(int $id): void`
  - `kembali(): void` sets `lihatId = null`

- [ ] **Step 1: Write `MasterAdminTest`**

```php
public function test_non_super_is_forbidden_on_master_admin(): void
{
    $this->actingAs($this->markasAdmin())
        ->get(route('admin.pengguna.admin'))
        ->assertForbidden();
}

public function test_creating_non_super_admin_requires_markas(): void
{
    Livewire::actingAs($this->superAdmin())
        ->test(MasterAdmin::class)
        ->set('nama', 'Staf Satu')
        ->set('email', 'staf1@example.com')
        ->set('password', 'secret123')
        ->set('is_super_admin', false)
        ->set('markas_id', '')
        ->call('tambah')
        ->assertHasErrors(['markas_id']);
}

public function test_creating_super_admin_without_markas_succeeds(): void
{
    Livewire::actingAs($this->superAdmin())
        ->test(MasterAdmin::class)
        ->set('nama', 'Super Dua')
        ->set('email', 'super2@example.com')
        ->set('password', 'secret123')
        ->set('is_super_admin', true)
        ->set('markas_id', '')
        ->call('tambah')
        ->assertHasNoErrors();

    $created = User::where('email', 'super2@example.com')->first();
    $this->assertTrue($created->isSuperAdmin());
    $this->assertSame(2, (int) $created->role_id);
    $this->assertSame([], $created->markasIds());
}

public function test_creating_non_super_admin_attaches_markas(): void
{
    $markas = Markas::create(['markas' => 'Genteng']);
    Livewire::actingAs($this->superAdmin())
        ->test(MasterAdmin::class)
        ->set('nama', 'Staf Dua')
        ->set('email', 'staf2@example.com')
        ->set('password', 'secret123')
        ->set('is_super_admin', false)
        ->set('markas_id', (string) $markas->id)
        ->call('tambah')
        ->assertHasNoErrors();

    $created = User::where('email', 'staf2@example.com')->first();
    $this->assertFalse($created->isSuperAdmin());
    $this->assertEquals([$markas->id], $created->markasIds());
    $this->assertTrue(Hash::check('secret123', $created->password));
}

public function test_cannot_delete_own_admin_account(): void
{
    $super = $this->superAdmin();
    Livewire::actingAs($super)
        ->test(MasterAdmin::class)
        ->call('hapus', $super->id)
        ->assertHasErrors(['hapus']);

    $this->assertDatabaseHas('users', ['id' => $super->id]);
}
```

Reuse `superAdmin()` / `markasAdmin()` by extracting a `tests/Concerns/CreatesAdminActors.php` with those two methods **in this task** if copy-paste across files is getting long — optional. Duplicating two methods is allowed.

Search: `assertSee` filtered name when `cari` set.

- [ ] **Step 2: Run MasterAdminTest, confirm FAIL**

Expected: FAIL (component missing).

- [ ] **Step 3: Implement component + view + route**

`#[Layout('layouts.panel-cakra')]` `#[Title('Admin')]`. `WithPagination`.

`tambah()` validation:

- `nama` required
- `email` required|email|unique:users,email
- `password` required|min:6
- `markas_id` required unless `is_super_admin` — message `Markas wajib untuk admin non-super`
- create user `role_id` 2, `is_super_admin`, hashed password, `nama`, `email`
- if `markas_id` filled, `$user->markas()->attach((int) $markas_id)`
- reset form fields

`hapus($id)`: if `(int) $id === (int) auth()->id()` then `$this->addError('hapus', 'Tidak bisa menghapus akun sendiri'); return;` else `User::where('role_id', 2)->findOrFail($id)->delete();`

List query: `User::where('role_id', 2)->when($this->cari, fn ($q) => $q->where(function ($q) { $q->where('nama', 'like', '%'.$this->cari.'%')->orWhere('email', 'like', '%'.$this->cari.'%'); }))->orderByDesc('id')->paginate(10)`.

View: cream card, search input `wire:model.live.debounce.400ms="cari"`, table Nama/Email/Flag/Markas/Aksi, form tambah, lihat panel when `$lihatId`. Gold `btn-ck`. Delete confirm `wire:click` + `wire:confirm` if available in Livewire 3 (`wire:confirm="Hapus akun ini?"`); else `onclick` confirm is acceptable matching old UI.

Non-super never renders this page (middleware).

- [ ] **Step 4: Run MasterAdminTest + AdminAccessTest**

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/Admin/MasterAdmin.php resources/views/livewire/admin/master-admin.blade.php routes/web.php tests/Feature/Admin/MasterAdminTest.php
git commit -m "Add superadmin-only Livewire admin master."
```

---

### Task 6: Livewire Master Pelajar

**Files:**
- Create: `app/Livewire/Admin/MasterPelajar.php`
- Create: `resources/views/livewire/admin/master-pelajar.blade.php`
- Create: `tests/Feature/Admin/MasterPelajarTest.php`
- Modify: `routes/web.php` — `MasterPelajar::class` named `admin.pengguna.pelajar` (replace Task 4 placeholder). Keep old `super.penggunapelajar` as a named redirect to the new route so `admin/administrasi` links do not 500: `Route::redirect('/pengguna-pelajar-legacy', '/admin/pengguna-pelajar');` is unnecessary if you **change** `super.penggunapelajar` to the Livewire action **or** add `->name('super.penggunapelajar')` as a second registration via `Route::get('/pengguna-pelajar', MasterPelajar::class)->name('admin.pengguna.pelajar');` and `Route::permanentRedirect` from nothing.

  Replace the old `PenggunaController@penggunaPelajar` GET `/pengguna-pelajar` with Livewire and **also** `->name('super.penggunapelajar')` is impossible (one name). Set Livewire name `admin.pengguna.pelajar`. Add:

  ```php
  Route::get('/pengguna-pelajar', MasterPelajar::class)->name('admin.pengguna.pelajar');
  ```

  Change `resources/views/admin/administrasi/index.blade.php` link `super.penggunapelajar` → `admin.pengguna.pelajar`. Leave other old pelajar controller routes (cetak, suspend list) under `superadmin-role` as they are.

  Optionally map `staf-admin/pengguna-pelajar` to `admin.pengguna.pelajar` now.

**Interfaces:**
- Consumes: `AdminVisibility::pelajarQuery`, `Pelajar` fillable fields
- Produces:
  - `public string $cari = ''`
  - `public string $halaman = 'daftar'` // `daftar|lihat|edit`
  - `public ?int $pelajarUserId = null`
  - `public $nik, $nisn, $tempat_lahir, $tanggal_lahir, $alamat, $sekolah, $wa, $ibu, $wali, $wa_wali, $markas_id`
  - `lihat(int $userId)`, `edit(int $userId)`, `simpan()`, `suspend(int $userId)`, `hapus(int $userId)`, `kembali()`
  - Do **not** re-upload foto in this task (keep existing `foto` value).

- [ ] **Step 1: Write `MasterPelajarTest`**

```php
public function test_non_super_only_sees_pelajar_in_assigned_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);

    $a = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Genteng']);
    $b = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Jember']);
    Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
    Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);

    Livewire::actingAs($admin)
        ->test(MasterPelajar::class)
        ->assertSee('Ada Di Genteng')
        ->assertDontSee('Ada Di Jember');
}

public function test_super_sees_pelajar_from_every_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $a = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Genteng']);
    $b = User::factory()->create(['role_id' => 4, 'nama' => 'Ada Di Jember']);
    Pelajar::create(['pelajar_id' => $a->id, 'markas_id' => $genteng->id]);
    Pelajar::create(['pelajar_id' => $b->id, 'markas_id' => $jember->id]);

    Livewire::actingAs($this->superAdmin())
        ->test(MasterPelajar::class)
        ->assertSee('Ada Di Genteng')
        ->assertSee('Ada Di Jember');
}

public function test_non_super_cannot_suspend_or_delete(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);
    $pelajar = User::factory()->create(['role_id' => 4]);
    Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

    Livewire::actingAs($admin)
        ->test(MasterPelajar::class)
        ->call('suspend', $pelajar->id)
        ->assertForbidden();

    Livewire::actingAs($admin)
        ->test(MasterPelajar::class)
        ->call('hapus', $pelajar->id)
        ->assertForbidden();

    $this->assertSame(4, (int) $pelajar->fresh()->role_id);
}

public function test_super_can_suspend_pelajar(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $pelajar = User::factory()->create(['role_id' => 4]);
    Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id]);

    Livewire::actingAs($this->superAdmin())
        ->test(MasterPelajar::class)
        ->call('suspend', $pelajar->id)
        ->assertHasNoErrors();

    $this->assertSame(6, (int) $pelajar->fresh()->role_id);
}

public function test_non_super_can_edit_biodata_in_own_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);
    $pelajar = User::factory()->create(['role_id' => 4]);
    $row = Pelajar::create(['pelajar_id' => $pelajar->id, 'markas_id' => $genteng->id, 'nik' => '111']);

    Livewire::actingAs($admin)
        ->test(MasterPelajar::class)
        ->call('edit', $pelajar->id)
        ->set('nik', '999')
        ->set('markas_id', (string) $genteng->id)
        ->call('simpan')
        ->assertHasNoErrors();

    $this->assertSame('999', $row->fresh()->nik);
}
```

Also: non-super `edit` on Jember pelajar → `assertForbidden()`.

- [ ] **Step 2: Run MasterPelajarTest, confirm FAIL**

- [ ] **Step 3: Implement**

Authorize a row: load `Pelajar` by `pelajar_id`. If actor not super and `markas_id` not in `markasIds()`, `abort(403)`.

List: `AdminVisibility::pelajarQuery(auth()->user())->with('pelajar.markas')` — **stop**. `pelajarQuery` returns `User` builder joined to `adm_pelajars`. Eager load: `->with(['pelajar.markas'])` on User (`pelajar()` HasOne exists). Search: `when($this->cari, fn ($q) => $q->where(function ($q) { $q->where('users.nama', 'like', ...)->orWhere('users.email', 'like', ...)->orWhere('users.nomor_registrasi', 'like', ...); }))`. Paginate 10.

`suspend` / `hapus`: `abort(403)` unless `auth()->user()->isSuperAdmin()`, then authorize markas (super skips). Suspend: `role_id = 6`. Hapus: delete `User` (pelajar row may remain orphan — old controller deleted user only after deleting foto file; match old: delete foto file if present then `$user->delete()`). Do not add new `adm_pelajars` columns.

`simpan`: validate existing biodata strings nullable; `markas_id` required|exists:adm_markas,id; if not super, `Rule::in($actor->markasIds())`. Update `Pelajar` by `pelajar_id`. `nama`/`email` stay read-only on the form.

View: table + lihat dl + edit form. Hide Suspend/Hapus buttons unless `isSuperAdmin()`.

- [ ] **Step 4: Run MasterPelajarTest + prior admin tests**

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/Admin/MasterPelajar.php resources/views/livewire/admin/master-pelajar.blade.php routes/web.php resources/views/admin/administrasi/index.blade.php tests/Feature/Admin/MasterPelajarTest.php
git commit -m "Add markas-scoped Livewire pelajar master."
```

---

### Task 7: Livewire Master Pendidik

**Files:**
- Create: `app/Livewire/Admin/MasterPendidik.php`
- Create: `resources/views/livewire/admin/master-pendidik.blade.php`
- Create: `tests/Feature/Admin/MasterPendidikTest.php`
- Modify: `routes/web.php` — replace placeholder with `MasterPendidik::class` name `admin.pengguna.pendidik`
- Modify: `resources/views/admin/administrasi/index.blade.php` — pendidik + staf links to `admin.pengguna.pendidik` and `admin.pengguna.admin`

**Interfaces:**
- Consumes: `AdminVisibility::pendidikQuery`, `Pendidik::DEFAULT_PASSWORD`, `Pendidik::create` with `mapel_id` 10
- Produces:
  - `public string $cari = ''`
  - `public string $nama = ''`
  - `public string $email = ''`
  - `public $markas_id = ''`
  - `public ?int $lihatId = null`
  - `tambah()`, `hapus(int $userId)`, `lihat(int $userId)`

- [ ] **Step 1: Write `MasterPendidikTest`**

```php
public function test_creating_pendidik_uses_default_password(): void
{
    $markas = Markas::create(['markas' => 'Genteng']);
    Livewire::actingAs($this->superAdmin())
        ->test(MasterPendidik::class)
        ->set('nama', 'Guru Baru')
        ->set('email', 'guru@example.com')
        ->set('markas_id', (string) $markas->id)
        ->call('tambah')
        ->assertHasNoErrors();

    $user = User::where('email', 'guru@example.com')->first();
    $this->assertSame(3, (int) $user->role_id);
    $this->assertTrue(Hash::check(Pendidik::DEFAULT_PASSWORD, $user->password));
    $this->assertDatabaseHas('adm_pendidik', [
        'pendidik_id' => $user->id,
        'markas_id' => $markas->id,
        'mapel_id' => 10,
    ]);
}

public function test_non_super_only_sees_pendidik_in_assigned_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);
    $a = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Genteng']);
    $b = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Jember']);
    Pendidik::create(['pendidik_id' => $a->id, 'mapel_id' => 10, 'markas_id' => $genteng->id]);
    Pendidik::create(['pendidik_id' => $b->id, 'mapel_id' => 10, 'markas_id' => $jember->id]);

    Livewire::actingAs($admin)
        ->test(MasterPendidik::class)
        ->assertSee('Guru Genteng')
        ->assertDontSee('Guru Jember');
}

public function test_non_super_cannot_assign_foreign_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);

    Livewire::actingAs($admin)
        ->test(MasterPendidik::class)
        ->set('nama', 'Guru X')
        ->set('email', 'gurux@example.com')
        ->set('markas_id', (string) $jember->id)
        ->call('tambah')
        ->assertHasErrors(['markas_id']);
}
```

- [ ] **Step 2: Run MasterPendidikTest, confirm FAIL**

- [ ] **Step 3: Implement**

`tambah`: validate nama, email unique, markas_id required|exists:adm_markas,id; non-super `Rule::in(auth()->user()->markasIds())`. If non-super has exactly one markas, `mount()` can prefill `markas_id`. Create user `role_id` 3, password `Hash::make(Pendidik::DEFAULT_PASSWORD)`. `Pendidik::create(['pendidik_id' => $user->id, 'mapel_id' => 10, 'markas_id' => (int) $this->markas_id])`. No aktif toggle.

`hapus`: authorize via pendidik `markas_id` like pelajar; delete user.

List: `AdminVisibility::pendidikQuery` + search nama/email + paginate 10.

Markas dropdown: super `Markas::orderBy('markas')->get()`; non-super only attached markas.

- [ ] **Step 4: Run MasterPendidikTest + full `tests/Feature/Admin` + FormLoginTest + WizardPendaftaranTest**

Expected: PASS all.

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/Admin/MasterPendidik.php resources/views/livewire/admin/master-pendidik.blade.php routes/web.php resources/views/admin/administrasi/index.blade.php tests/Feature/Admin/MasterPendidikTest.php
git commit -m "Add markas-scoped Livewire pendidik master."
```

---

## Self-review (spec coverage)

| Spec item | Task |
| --- | --- |
| `is_super_admin` + migrate 2/7 | 1 |
| `admin_markas` + `markasIds` + visibility | 1 |
| Login → `admin.beranda` | 2 |
| CAT/dinas super only, 403 | 3 |
| `/staf-admin` redirect | 3 |
| Panel layout, beranda, nav | 4 |
| Master admin CRUD + cannot delete self + markas required | 5 |
| Master pelajar scope, edit, super suspend/hapus | 6 |
| Master pendidik + `pendidik123` | 7 |
| No `adm_pelajars` schema change | 6 (updates only existing columns) |
| Visual tokens | 4 |
| Tests 1–6 in spec | 1, 2, 3, 5, 6, 7 |

No CAT restyle. No absensi. Multi-markas UI is one select only.
