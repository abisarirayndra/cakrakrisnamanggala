# Weekly Jadwal Composer Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship a Livewire weekly (Monday–Sunday) jadwal composer at `/admin/jadwal` scoped by kelas markas, writing dated rows into existing `adm_jadwal`.

**Architecture:** `AdminVisibility` filters kelas/jadwal/pendidik by markas. `JadwalMingguan` lists one kelas for one week, grouped by day; the sidebar form creates or updates a slot. No new tables. Absensi stays on old routes.

**Tech Stack:** Laravel 12, PHP 8.3, Livewire v3, Alpine, Bootstrap 5.3, PHPUnit 11, SQLite in-memory.

**Spec:** `docs/superpowers/specs/2026-09-12-admin-jadwal-mingguan-design.md`

## Global Constraints

- PHP 8.3 via `C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64\php.exe` (default PATH PHP is 7.4).
- PHPUnit: `$env:PATH = "C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64;" + $env:PATH; php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit …`
- Livewire v3 + Alpine + Bootstrap 5.3; Plus Jakarta Sans; cream `#F6F3EE`, navy `#243044`, gold `#B8954A`.
- Do not change `adm_pelajars` columns. Do not add jadwal template tables.
- Visibility is markas via `kelas.markas_id`, not `staf_id`.
- `staf_id` on create = `auth()->id()`.
- Hapus/edit blocked when absensi pelajar or pendidik exists for that slot.
- Out of scope: QR absensi, jurnal, rekap, copy-last-week.

## File map

- Modify: `tests/Concerns/CreatesAdminMasterSchema.php` — full `adm_jadwal` columns + stub absensi tables
- Modify: `app/Support/AdminVisibility.php` — `kelasForJadwal`, `jadwalQuery`, `pendidikForKelas`
- Create: `tests/Feature/Admin/JadwalMingguanTest.php`
- Create: `app/Livewire/Admin/JadwalMingguan.php`
- Create: `resources/views/livewire/admin/jadwal-mingguan.blade.php`
- Modify: `app/Jadwal.php` — `sudahAdaAbsensi()` helper
- Modify: `routes/web.php` — `admin.jadwal`; redirect `staf-admin.jadwal`
- Modify: `resources/views/layouts/partials/admin-sidebar-nav.blade.php`
- Modify: `tests/Feature/Admin/AdminAccessTest.php` — sidebar href
- Modify: `public/css/cakra-admin.css` — day section spacing if needed

---

### Task 1: Schema stubs and visibility queries

**Files:**
- Modify: `tests/Concerns/CreatesAdminMasterSchema.php`
- Modify: `app/Support/AdminVisibility.php`
- Modify: `tests/Feature/Admin/AdminIdentityTest.php` (add two query tests) or fold them into `JadwalMingguanTest.php`
- Test: `tests/Feature/Admin/JadwalMingguanTest.php` (create the file with query tests only in this task)

**Interfaces:**
- Consumes: `User::isSuperAdmin()`, `User::markasIds()`, `Kelas`, `Jadwal`, `Pendidik`
- Produces:
  - `AdminVisibility::kelasForJadwal(User $actor): Builder`
  - `AdminVisibility::jadwalQuery(User $actor): Builder`
  - `AdminVisibility::pendidikForKelas(Kelas $kelas): Builder`

- [ ] **Step 1: Expand the in-memory schema**

In `CreatesAdminMasterSchema::setUpAdminMasterSchema()`, drop `adm_absensi_pendidik` and `adm_absensi_pelajar` before `adm_jadwal`. Replace the `adm_jadwal` create with:

```php
Schema::create('adm_jadwal', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('staf_id')->nullable();
    $table->unsignedInteger('mapel_id')->nullable();
    $table->unsignedInteger('pendidik_id')->nullable();
    $table->unsignedInteger('kelas_id')->nullable();
    $table->dateTime('mulai')->nullable();
    $table->dateTime('selesai')->nullable();
    $table->timestamps();
});

Schema::create('adm_absensi_pelajar', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('jadwal_id');
    $table->unsignedInteger('pelajar_id')->nullable();
    $table->dateTime('datang')->nullable();
    $table->dateTime('pulang')->nullable();
    $table->integer('status')->nullable();
    $table->timestamps();
});

Schema::create('adm_absensi_pendidik', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('jadwal_id');
    $table->unsignedInteger('pendidik_id')->nullable();
    $table->dateTime('datang')->nullable();
    $table->dateTime('pulang')->nullable();
    $table->integer('status')->nullable();
    $table->timestamps();
});
```

Keep `mapel_id` nullable so existing `MasterMapelTest` insert still works.

- [ ] **Step 2: Write failing visibility tests**

Create `tests/Feature/Admin/JadwalMingguanTest.php` with the same sqlite env + `CreatesAdminMasterSchema` as `MasterKelasTest`. Add:

```php
public function test_admin_kelas_for_jadwal_is_limited_to_assigned_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);
    $a = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
    Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
    Kelas::create(['nama' => 'Tanpa Markas', 'markas_id' => null]);

    $ids = AdminVisibility::kelasForJadwal($admin)->pluck('kelas.id')->all();
    $this->assertEquals([$a->id], $ids);
}

public function test_admin_jadwal_query_hides_other_markas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);
    $ka = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
    $kb = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
    $mapel = Mapel::create(['mapel' => 'Matematika']);
    $guru = User::factory()->create(['role_id' => 3]);
    $mine = Jadwal::create([
        'staf_id' => $admin->id,
        'mapel_id' => $mapel->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $ka->id,
        'mulai' => '2026-09-14 08:00:00',
        'selesai' => '2026-09-14 09:00:00',
    ]);
    Jadwal::create([
        'staf_id' => $admin->id,
        'mapel_id' => $mapel->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $kb->id,
        'mulai' => '2026-09-14 08:00:00',
        'selesai' => '2026-09-14 09:00:00',
    ]);

    $ids = AdminVisibility::jadwalQuery($admin)->pluck('adm_jadwal.id')->all();
    $this->assertEquals([$mine->id], $ids);
}
```

- [ ] **Step 3: Run tests, confirm FAIL**

Run:

```powershell
$env:PATH = "C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64;" + $env:PATH
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit tests/Feature/Admin/JadwalMingguanTest.php
```

Expected: FAIL (`kelasForJadwal` / `jadwalQuery` missing, or `adm_jadwal` missing columns).

- [ ] **Step 4: Implement helpers**

On `AdminVisibility`:

```php
public static function kelasForJadwal(User $actor): Builder
{
    $query = Kelas::query()->whereNotNull('markas_id')->orderBy('nama');

    if (! $actor->isSuperAdmin()) {
        $ids = $actor->markasIds();
        $query = $ids === [] ? $query->whereRaw('1 = 0') : $query->whereIn('markas_id', $ids);
    }

    return $query;
}

public static function jadwalQuery(User $actor): Builder
{
    $query = Jadwal::query()
        ->join('kelas', 'kelas.id', '=', 'adm_jadwal.kelas_id')
        ->select('adm_jadwal.*');

    if (! $actor->isSuperAdmin()) {
        $ids = $actor->markasIds();
        $query = $ids === [] ? $query->whereRaw('1 = 0') : $query->whereIn('kelas.markas_id', $ids);
    }

    return $query;
}

public static function pendidikForKelas(Kelas $kelas): Builder
{
    return User::query()
        ->join('adm_pendidik', 'adm_pendidik.pendidik_id', '=', 'users.id')
        ->where('users.role_id', 3)
        ->where('adm_pendidik.markas_id', $kelas->markas_id)
        ->select('users.*')
        ->orderBy('users.nama');
}
```

Add `use App\Jadwal; use App\Kelas;` at the top of `AdminVisibility`.

- [ ] **Step 5: Run JadwalMingguanTest + MasterMapelTest**

Expected: new tests PASS; `MasterMapelTest` still PASS (hapus blocked by jadwal row).

- [ ] **Step 6: Commit** (only if the user asked for commits)

```
feat: add markas-scoped jadwal visibility queries
```

---

### Task 2: Livewire page, week list, access

**Files:**
- Create: `app/Livewire/Admin/JadwalMingguan.php`
- Create: `resources/views/livewire/admin/jadwal-mingguan.blade.php`
- Modify: `routes/web.php`
- Modify: `tests/Feature/Admin/JadwalMingguanTest.php`

**Interfaces:**
- Consumes: helpers from Task 1
- Produces: `JadwalMingguan` with `kelas_id`, `senin`, `boot()`, `updatedSenin()`, `render()` listing slots for the selected week

- [ ] **Step 1: Write failing access + list tests**

```php
public function test_non_admin_cannot_mount_jadwal_mingguan(): void
{
    Livewire::actingAs(User::factory()->create(['role_id' => 4]))
        ->test(JadwalMingguan::class)
        ->assertForbidden();
}

public function test_list_shows_only_slots_in_selected_week_and_kelas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $kelas = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
    $mapel = Mapel::create(['mapel' => 'Matematika']);
    $guru = User::factory()->create(['role_id' => 3, 'nama' => 'Guru Satu']);
    Jadwal::create([
        'staf_id' => 1,
        'mapel_id' => $mapel->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'mulai' => '2026-09-14 08:00:00',
        'selesai' => '2026-09-14 09:00:00',
    ]);
    Jadwal::create([
        'staf_id' => 1,
        'mapel_id' => $mapel->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'mulai' => '2026-09-21 08:00:00',
        'selesai' => '2026-09-21 09:00:00',
    ]);

    Livewire::actingAs($this->superAdmin())
        ->test(JadwalMingguan::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('senin', '2026-09-14')
        ->assertSee('Matematika')
        ->assertSee('Guru Satu')
        ->assertSee('08:00')
        ->assertDontSee('2026-09-21');
}
```

14 Sep 2026 is a Monday.

- [ ] **Step 2: Run, confirm FAIL** (component missing)

- [ ] **Step 3: Implement component + route + view (list only)**

`app/Livewire/Admin/JadwalMingguan.php`:

```php
#[Layout('layouts.panel-cakra')]
#[Title('Jadwal')]
class JadwalMingguan extends Component
{
    public string $kelas_id = '';
    public string $senin = '';
    public string $hari = '0';
    public string $mapel_id = '';
    public string $pendidik_id = '';
    public string $jam_mulai = '';
    public string $jam_selesai = '';
    public ?int $editId = null;

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $this->senin = now()->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    public function updatedSenin(): void
    {
        if ($this->senin === '') {
            return;
        }
        $this->senin = Carbon::parse($this->senin)->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->batal();
    }

    public function updatedKelasId(): void
    {
        $this->batal();
    }

    public function batal(): void
    {
        $this->editId = null;
        $this->reset(['hari', 'mapel_id', 'pendidik_id', 'jam_mulai', 'jam_selesai']);
        $this->hari = '0';
        $this->resetErrorBag();
    }

    public function render()
    {
        $actor = auth()->user();
        $start = Carbon::parse($this->senin)->startOfDay();
        $end = $start->copy()->addDays(6)->endOfDay();

        $slots = collect();
        if ($this->kelas_id !== '') {
            $slots = AdminVisibility::jadwalQuery($actor)
                ->with(['mapel', 'pendidik', 'kelas'])
                ->where('adm_jadwal.kelas_id', $this->kelas_id)
                ->whereBetween('adm_jadwal.mulai', [$start, $end])
                ->orderBy('adm_jadwal.mulai')
                ->get();
        }

        $kelasAktif = $this->kelas_id === ''
            ? null
            : AdminVisibility::kelasForJadwal($actor)->whereKey($this->kelas_id)->first();

        return view('livewire.admin.jadwal-mingguan', [
            'kelasList' => AdminVisibility::kelasForJadwal($actor)->with('markas')->get(),
            'mapelList' => Mapel::orderBy('mapel')->get(),
            'pendidikList' => $kelasAktif
                ? AdminVisibility::pendidikForKelas($kelasAktif)->get()
                : collect(),
            'hariList' => [
                0 => 'Senin',
                1 => 'Selasa',
                2 => 'Rabu',
                3 => 'Kamis',
                4 => 'Jumat',
                5 => 'Sabtu',
                6 => 'Minggu',
            ],
            'slotsByDay' => $slots->groupBy(fn (Jadwal $row) => $row->mulai->toDateString()),
            'seninCarbon' => $start,
        ]);
    }
}
```

View: page title Jadwal; filters kelas + date `senin`; hint “Minggu Senin–Minggu”; if no kelas, “Pilih kelas”. Else seven sections from `$seninCarbon` + offset, slots from `$slotsByDay`. Empty: “Tidak ada slot”.

In `routes/web.php` admin group (next to pelajar/pendidik, **not** inside `superadmin-role`):

```php
Route::get('/jadwal', \App\Livewire\Admin\JadwalMingguan::class)->name('admin.jadwal');
```

Import the class at the top of `web.php`.

- [ ] **Step 4: Run JadwalMingguanTest**

Expected: access + list PASS.

- [ ] **Step 5: Commit** (if asked)

```
feat: add weekly jadwal Livewire list
```

---

### Task 3: Create slot, overlap, foreign-markas 403

**Files:**
- Modify: `app/Livewire/Admin/JadwalMingguan.php` — `simpan()`
- Modify: `resources/views/livewire/admin/jadwal-mingguan.blade.php` — form
- Modify: `tests/Feature/Admin/JadwalMingguanTest.php`

**Interfaces:**
- Produces: `simpan(): void` creating `Jadwal` with `staf_id = auth()->id()` and datetimes `senin + hari + jam_*`

- [ ] **Step 1: Write failing tests**

```php
public function test_admin_can_create_slot_in_own_markas(): void
{
    [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();

    Livewire::actingAs($admin)
        ->test(JadwalMingguan::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('senin', '2026-09-14')
        ->set('hari', '0')
        ->set('mapel_id', (string) $mapel->id)
        ->set('pendidik_id', (string) $guru->id)
        ->set('jam_mulai', '08:00')
        ->set('jam_selesai', '09:30')
        ->call('simpan')
        ->assertHasNoErrors();

    $row = Jadwal::firstOrFail();
    $this->assertSame($admin->id, (int) $row->staf_id);
    $this->assertSame('2026-09-14 08:00:00', $row->mulai->format('Y-m-d H:i:s'));
    $this->assertSame('2026-09-14 09:30:00', $row->selesai->format('Y-m-d H:i:s'));
}

public function test_overlapping_slot_is_rejected(): void
{
    [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
    Jadwal::create([
        'staf_id' => $admin->id,
        'mapel_id' => $mapel->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'mulai' => '2026-09-14 08:00:00',
        'selesai' => '2026-09-14 09:00:00',
    ]);

    Livewire::actingAs($admin)
        ->test(JadwalMingguan::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('senin', '2026-09-14')
        ->set('hari', '0')
        ->set('mapel_id', (string) $mapel->id)
        ->set('pendidik_id', (string) $guru->id)
        ->set('jam_mulai', '08:30')
        ->set('jam_selesai', '09:30')
        ->call('simpan')
        ->assertHasErrors(['jam_mulai']);
}

public function test_admin_cannot_create_slot_for_other_markas_kelas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $jember = Markas::create(['markas' => 'Jember']);
    $admin = User::factory()->create(['role_id' => 2, 'is_super_admin' => false]);
    $admin->markas()->attach($genteng->id);
    $kelas = Kelas::create(['nama' => 'B', 'markas_id' => $jember->id]);
    $mapel = Mapel::create(['mapel' => 'Matematika']);
    $guru = User::factory()->create(['role_id' => 3]);
    Pendidik::create(['pendidik_id' => $guru->id, 'markas_id' => $jember->id]);

    Livewire::actingAs($admin)
        ->test(JadwalMingguan::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('senin', '2026-09-14')
        ->set('hari', '0')
        ->set('mapel_id', (string) $mapel->id)
        ->set('pendidik_id', (string) $guru->id)
        ->set('jam_mulai', '08:00')
        ->set('jam_selesai', '09:00')
        ->call('simpan')
        ->assertHasErrors(['kelas_id']);
}
```

`ownMarkasFixture()`: Genteng markas, admin attached, kelas A, mapel, guru role 3 with `Pendidik` `markas_id` Genteng.

- [ ] **Step 2: Run, confirm FAIL** (`simpan` missing)

- [ ] **Step 3: Implement `simpan()` + form**

Private `kelasTerpilih(): Kelas` — `AdminVisibility::kelasForJadwal(auth()->user())->whereKey($this->kelas_id)->firstOrFail()` (validation should fail first).

Build datetimes:

```php
$mulai = Carbon::parse($this->senin)->startOfWeek(Carbon::MONDAY)
    ->addDays((int) $this->hari)
    ->setTimeFromTimeString($this->jam_mulai);
$selesai = $mulai->copy()->setTimeFromTimeString($this->jam_selesai);
```

Overlap:

```php
$bentrok = AdminVisibility::jadwalQuery(auth()->user())
    ->where('adm_jadwal.kelas_id', $this->kelas_id)
    ->where('adm_jadwal.id', '!=', $this->editId ?? 0)
    ->where('adm_jadwal.mulai', '<', $selesai)
    ->where('adm_jadwal.selesai', '>', $mulai)
    ->exists();
```

If bentrok: `$this->addError('jam_mulai', 'Jam bentrok dengan slot lain'); return;`

Create payload includes `staf_id` => auth id. After save, `batal()`.

Form: `wire:submit="simpan"`. Fields hari (select 0–6), mapel, pendidik, jam_mulai, jam_selesai (`type="time"`). Submit “Tambah slot”. Disable when `kelas_id` empty.

Validate `kelas_id` with `Rule::in(AdminVisibility::kelasForJadwal($actor)->pluck('kelas.id')->all())` — pluck `id` from Kelas builder (`kelas.id` if joined; here it is `Kelas::query()` so `pluck('id')`).

`pendidik_id` `Rule::in(AdminVisibility::pendidikForKelas($kelas)->pluck('users.id'))`.

- [ ] **Step 4: Run tests**

Expected: PASS.

- [ ] **Step 5: Commit** (if asked)

```
feat: allow weekly jadwal slot create with overlap checks
```

---

### Task 4: Edit, hapus, absensi guard

**Files:**
- Modify: `app/Jadwal.php` — `sudahAdaAbsensi(): bool`
- Modify: `app/Livewire/Admin/JadwalMingguan.php` — `ubah`, `hapus`, `simpan` update branch
- Modify: view — Ubah/Hapus buttons, Batal
- Modify: tests

**Interfaces:**
- Produces: `Jadwal::sudahAdaAbsensi(): bool`, `ubah(int $id)`, `hapus(int $id)`

- [ ] **Step 1: Write failing tests**

```php
public function test_can_update_slot_times(): void
{
    [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
    $row = Jadwal::create([/* Monday 08:00-09:00 */]);

    Livewire::actingAs($admin)
        ->test(JadwalMingguan::class)
        ->call('ubah', $row->id)
        ->set('jam_mulai', '10:00')
        ->set('jam_selesai', '11:00')
        ->call('simpan')
        ->assertHasNoErrors();

    $this->assertSame('10:00', $row->fresh()->mulai->format('H:i'));
}

public function test_hapus_blocked_when_absensi_exists(): void
{
    [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
    $row = Jadwal::create([/* ... */]);
    AbsensiPelajar::create([
        'jadwal_id' => $row->id,
        'pelajar_id' => User::factory()->create(['role_id' => 4])->id,
        'datang' => '2026-09-14 08:00:00',
        'status' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(JadwalMingguan::class)
        ->call('hapus', $row->id)
        ->assertHasErrors(['hapus']);

    $this->assertDatabaseHas('adm_jadwal', ['id' => $row->id]);
}

public function test_admin_cannot_hapus_other_markas_slot(): void
{
    // create slot on Jember kelas, act as Genteng admin → 403
}
```

Also: updating a row with absensi → `assertHasErrors(['hapus'])` or a dedicated `jadwal` key. Spec says message `Jadwal sudah dipakai absensi`. Use error key `hapus` for hapus and `jam_mulai` or `editId` for update — **use `hapus` for hapus and `mapel_id` for blocked edit** is messy. Spec: both use the same copy. Use bag key `jadwal` for both, tests `assertHasErrors(['jadwal'])`.

Lock the key as `jadwal`.

- [ ] **Step 2: Run, confirm FAIL**

- [ ] **Step 3: Implement**

```php
public function sudahAdaAbsensi(): bool
{
    return $this->absensiPelajar()->exists() || $this->absensiPendidik()->exists();
}
```

`authorizeRow(int $id): Jadwal` — `AdminVisibility::jadwalQuery($actor)->where('adm_jadwal.id', $id)->first()` then 404/403.

`ubah`: authorize, if absensi addError jadwal and return; else fill fields, `editId = $id`.

`hapus`: authorize, if absensi addError, else delete.

`simpan` when `editId`: do not change `staf_id`; if absensi, error and return.

View: each slot Ubah + Hapus (`wire:confirm="Hapus slot ini?"`). Form heading `$editId ? 'Ubah slot' : 'Tambah slot'`. Batal when editing.

- [ ] **Step 4: Run full `JadwalMingguanTest`**

Expected: PASS.

- [ ] **Step 5: Commit** (if asked)

```
feat: edit and delete weekly jadwal slots with absensi guard
```

---

### Task 5: Sidebar, old URL redirect, CSS, access test

**Files:**
- Modify: `resources/views/layouts/partials/admin-sidebar-nav.blade.php`
- Modify: `routes/web.php` (`staf-admin.jadwal` GET → redirect)
- Modify: `tests/Feature/Admin/AdminAccessTest.php`
- Modify: `public/css/cakra-admin.css` if day sections need it
- Modify: `resources/views/admin/beranda.blade.php` Jadwal button href

**Interfaces:**
- Produces: sidebar `route('admin.jadwal')`, `data-nav="jadwal"` active on `admin.jadwal`; `GET /staf-admin/jadwal` 302 to `/admin/jadwal` still named `staf-admin.jadwal`

- [ ] **Step 1: Write failing test in AdminAccessTest**

Change `assertSee(route('staf-admin.jadwal', absolute: false)` to `assertSee(route('admin.jadwal', absolute: false)`. Add:

```php
public function test_legacy_jadwal_index_redirects_to_admin_jadwal(): void
{
    $this->actingAs($this->markasAdmin())
        ->get(route('staf-admin.jadwal'))
        ->assertRedirect(route('admin.jadwal'));
}
```

Keep `assertTrue(Route::has('staf-admin.jadwal'))`.

- [ ] **Step 2: Run AdminAccessTest, confirm FAIL** (href still old)

- [ ] **Step 3: Wire routes and nav**

Replace the staf-admin index route with:

```php
Route::get('/jadwal', fn () => redirect()->route('admin.jadwal'))->name('staf-admin.jadwal');
```

Do **not** rename `staf-admin.jadwal.tambah` etc.

Sidebar:

```php
<a href="{{ route('admin.jadwal') }}" data-nav="jadwal" class="ck-nav-link {{ $active('admin.jadwal') }}">
```

Beranda ghost button: `route('admin.jadwal')`.

Optional CSS:

```css
.ck-day-block + .ck-day-block {
    margin-top: 1.25rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--ck-line);
}
```

- [ ] **Step 4: Run AdminAccessTest + JadwalMingguanTest + full PHPUnit**

```powershell
$env:PATH = "C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64;" + $env:PATH
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit
```

Expected: 0 failures.

- [ ] **Step 5: Browser check**

Open `/admin/jadwal` as superadmin. Pick a kelas that has `markas_id`. Set week to a Monday. Add one slot (mapel + pendidik of that markas + jam). Confirm it appears under the correct day. Click Ubah, change jam, Simpan. Do not hapus production rows that may have absensi.

- [ ] **Step 6: Commit** (if asked)

```
feat: point admin jadwal nav at the weekly composer
```

---

## Spec coverage

| Spec item | Task |
| --- | --- |
| `kelasForJadwal` / `jadwalQuery` / `pendidikForKelas` | 1 |
| `/admin/jadwal` Livewire, admin-role | 2 |
| Week Monday–Sunday list grouped by day | 2 |
| Create slot, `staf_id`, overlap | 3 |
| Other-markas 403/validation | 3 |
| Edit / hapus / absensi guard | 4 |
| Sidebar + legacy redirect | 5 |
| Absensi QR / datang-pulang | out of scope |

## Placeholder scan

No TBD. Error key for absensi guard is `jadwal`. Overlap key is `jam_mulai`.
