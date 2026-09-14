# Admin Slot Attendance Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ship a Livewire admin absensi desk at `/admin/absensi` that records datang/pulang (USB QR `nomor_registrasi` + Enter) and Izin/Sakit/Alpa against today’s jadwal slots.

**Architecture:** Reuse `AdminVisibility` markas queries. `AbsensiSlot` binds one kelas + one today `adm_jadwal` row, then writes existing `adm_absensi_pelajar` / `adm_absensi_pendidik` rows. No new tables. Pendidik checklist is out of scope.

**Tech Stack:** Laravel 12, PHP 8.3, Livewire v3, Alpine, Bootstrap 5.3, PHPUnit 11, SQLite in-memory.

**Spec:** `docs/superpowers/specs/2026-09-12-admin-absensi-slot-design.md`

## Global Constraints

- PHP 8.3 via `C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64\php.exe` (default PATH PHP is 7.4).
- PHPUnit: `$env:PATH = "C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64;" + $env:PATH; php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit …`
- Livewire v3 + Alpine + Bootstrap 5.3; Plus Jakarta Sans; cream `#F6F3EE`, navy `#243044`, gold `#B8954A`.
- Do not change `adm_pelajars` columns. Do not add absensi tables.
- Pelajar kelas is `users.kelas_id` (spec said `adm_pelajars.kelas_id`; that column does not exist and must not be added).
- Visibility is markas via `kelas.markas_id`, not `staf_id`.
- Status: `1` Hadir, `2` Izin, `3` Sakit, `4` Alpa.
- Scan window: `[mulai − 1 hour, selesai + 1 hour]`.
- Keterangan wajib only for Izin. Jurnal wajib only on guru utama pulang.
- Out of scope: pendidik checklist, webcam, card print, rekap, absensi staf.

## File map

- Modify: `tests/Concerns/CreatesAdminMasterSchema.php` — `keterangan` + `jurnal` on absensi stubs
- Create: `app/Support/AbsensiStatus.php` — integer constants
- Modify: `app/Support/AdminVisibility.php` — `pelajarForKelas(Kelas $kelas): Builder`
- Create: `tests/Feature/Admin/AbsensiSlotTest.php`
- Create: `app/Livewire/Admin/AbsensiSlot.php`
- Create: `resources/views/livewire/admin/absensi-slot.blade.php`
- Modify: `routes/web.php` — `admin.absensi`; redirect legacy desk routes
- Modify: `resources/views/layouts/partials/admin-sidebar-nav.blade.php`
- Modify: `resources/views/admin/beranda.blade.php`
- Modify: `tests/Feature/Admin/AdminAccessTest.php`

---

### Task 1: Schema stubs, status constants, pelajarForKelas

**Files:**
- Modify: `tests/Concerns/CreatesAdminMasterSchema.php`
- Create: `app/Support/AbsensiStatus.php`
- Modify: `app/Support/AdminVisibility.php`
- Test: `tests/Feature/Admin/AbsensiSlotTest.php` (create; query tests only)

**Interfaces:**
- Consumes: `Kelas`, `User`, `CreatesAdminMasterSchema`
- Produces: `AbsensiStatus::{HADIR,IZIN,SAKIT,ALPA}`, `AdminVisibility::pelajarForKelas(Kelas $kelas): Builder`

- [ ] **Step 1: Expand absensi stubs**

In `CreatesAdminMasterSchema`, add `$table->string('keterangan')->nullable();` to both absensi tables, and `$table->text('jurnal')->nullable();` to `adm_absensi_pendidik` only. Do not add columns to `adm_pelajars`.

- [ ] **Step 2: Write failing tests**

Create `tests/Feature/Admin/AbsensiSlotTest.php` with the same sqlite env + trait as `JadwalMingguanTest`. Add:

```php
public function test_pelajar_for_kelas_is_only_aktif_in_that_kelas(): void
{
    $genteng = Markas::create(['markas' => 'Genteng']);
    $ka = Kelas::create(['nama' => 'A', 'markas_id' => $genteng->id]);
    $kb = Kelas::create(['nama' => 'B', 'markas_id' => $genteng->id]);
    $mine = User::factory()->create(['role_id' => 4, 'kelas_id' => $ka->id, 'nama' => 'Siswa A']);
    User::factory()->create(['role_id' => 4, 'kelas_id' => $kb->id, 'nama' => 'Siswa B']);
    User::factory()->create(['role_id' => 6, 'kelas_id' => $ka->id, 'nama' => 'Suspended']);
    Pelajar::create(['pelajar_id' => $mine->id, 'markas_id' => $genteng->id]);

    $ids = AdminVisibility::pelajarForKelas($ka)->pluck('users.id')->all();
    $this->assertEquals([$mine->id], $ids);
}
```

If `pluck('users.id')` fails because the builder has no join, use `pluck('id')` — `pelajarForKelas` is `User::query()` with `where` on `users.kelas_id`, so **`pluck('id')`**.

- [ ] **Step 3: Run, confirm FAIL**

```powershell
$env:PATH = "C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64;" + $env:PATH
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/bin/phpunit tests/Feature/Admin/AbsensiSlotTest.php
```

Expected: FAIL (`pelajarForKelas` missing).

- [ ] **Step 4: Implement**

```php
final class AbsensiStatus
{
    public const HADIR = 1;
    public const IZIN = 2;
    public const SAKIT = 3;
    public const ALPA = 4;
}
```

```php
public static function pelajarForKelas(Kelas $kelas): Builder
{
    return User::query()
        ->where('users.role_id', 4)
        ->where('users.kelas_id', $kelas->id)
        ->orderBy('users.nama');
}
```

- [ ] **Step 5: Run AbsensiSlotTest**

Expected: PASS.

- [ ] **Step 6: Commit**

```
feat: add pelajar-for-kelas visibility helper
```

---

### Task 2: Livewire page, today’s slots, access

**Files:**
- Create: `app/Livewire/Admin/AbsensiSlot.php`
- Create: `resources/views/livewire/admin/absensi-slot.blade.php`
- Modify: `routes/web.php`
- Modify: `tests/Feature/Admin/AbsensiSlotTest.php`

**Interfaces:**
- Consumes: `AdminVisibility::{kelasForJadwal,jadwalQuery,pendidikForKelas,pelajarForKelas}`
- Produces: `AbsensiSlot` with `kelas_id`, `jadwal_id`, `mode`, `boot()`, `render()` listing today’s slots

- [ ] **Step 1: Write failing tests**

```php
public function test_non_admin_cannot_mount_absensi_slot(): void
{
    Livewire::actingAs(User::factory()->create(['role_id' => 4]))
        ->test(AbsensiSlot::class)
        ->assertForbidden();
}

public function test_list_shows_only_todays_slots_for_selected_kelas(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru] = $this->ownMarkasFixture();
    $fisika = Mapel::create(['mapel' => 'Fisika']);
    $today = Jadwal::create([
        'staf_id' => $admin->id,
        'mapel_id' => $mapel->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'mulai' => '2026-09-14 08:00:00',
        'selesai' => '2026-09-14 09:00:00',
    ]);
    Jadwal::create([
        'staf_id' => $admin->id,
        'mapel_id' => $fisika->id,
        'pendidik_id' => $guru->id,
        'kelas_id' => $kelas->id,
        'mulai' => '2026-09-15 08:00:00',
        'selesai' => '2026-09-15 09:00:00',
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->assertSee('Matematika')
        ->assertSee('08:00')
        ->assertDontSee('Fisika');
}

`ownMarkasFixture()`: copy from `JadwalMingguanTest` (Genteng, admin, kelas A, mapel Matematika, guru role 3 + `Pendidik`). `Carbon::setTestNow(null)` in `tearDown` when time is frozen.

- [ ] **Step 2: Run, confirm FAIL** (component missing)

- [ ] **Step 3: Implement component + route + view (list only)**

```php
#[Layout('layouts.panel-cakra')]
#[Title('Absensi')]
class AbsensiSlot extends Component
{
    public string $kelas_id = '';
    public string $jadwal_id = '';
    public string $mode = 'datang';
    public string $token = '';
    public string $jurnal = '';
    public string $pesan = '';
    public string $izin_user_id = '';
    public string $izin_status = '2';
    public string $izin_keterangan = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function updatedKelasId(): void
    {
        $this->jadwal_id = '';
        $this->reset(['token', 'jurnal', 'pesan', 'izin_user_id', 'izin_keterangan']);
        $this->izin_status = '2';
        $this->mode = 'datang';
        $this->resetErrorBag();
    }

    public function render()
    {
        $actor = auth()->user();
        $kelasAktif = $this->kelas_id === ''
            ? null
            : AdminVisibility::kelasForJadwal($actor)->whereKey($this->kelas_id)->first();

        $slots = collect();
        if ($kelasAktif) {
            $slots = AdminVisibility::jadwalQuery($actor)
                ->with(['mapel', 'pendidik'])
                ->where('adm_jadwal.kelas_id', $kelasAktif->id)
                ->whereDate('adm_jadwal.mulai', now()->toDateString())
                ->orderBy('adm_jadwal.mulai')
                ->get();
        }

        $slot = $this->jadwal_id === ''
            ? null
            : $slots->firstWhere('id', (int) $this->jadwal_id);

        return view('livewire.admin.absensi-slot', [
            'kelasList' => AdminVisibility::kelasForJadwal($actor)->with('markas')->get(),
            'slots' => $slots,
            'slot' => $slot,
            'hadirPendidik' => $slot ? AbsensiPendidik::query()->where('jadwal_id', $slot->id)->with('pendidik')->get() : collect(),
            'hadirPelajar' => $slot ? AbsensiPelajar::query()->where('jadwal_id', $slot->id)->with('pelajar')->get() : collect(),
            'pendidikList' => $kelasAktif ? AdminVisibility::pendidikForKelas($kelasAktif)->get() : collect(),
            'pelajarList' => $kelasAktif ? AdminVisibility::pelajarForKelas($kelasAktif)->get() : collect(),
        ]);
    }
}
```

View: filter card (kelas + slot select). Empty kelas: “Pilih kelas”. Empty slots: “Tidak ada mapel hari ini”. Slot options: `{mapel} · {H:i}–{H:i}`. Roster + work cards may be empty shells this task (no `scan()` yet). Work card disabled when no slot.

Route in admin group next to `admin.jadwal`, **not** inside `superadmin-role`:

```php
Route::get('/absensi', \App\Livewire\Admin\AbsensiSlot::class)->name('admin.absensi');
```

Import the class at the top of `web.php`.

- [ ] **Step 4: Run AbsensiSlotTest**

Expected: access + list PASS.

- [ ] **Step 5: Commit**

```
feat: add admin absensi desk for today's slots
```

---

### Task 3: Scan datang

**Files:**
- Modify: `app/Livewire/Admin/AbsensiSlot.php` — `scan()`, `slotAktif()`
- Modify: view — scan input `wire:submit="scan"`, autofocus
- Modify: tests

**Interfaces:**
- Produces: `scan(): void` writing Hadir datang; `slotAktif(): Jadwal`

- [ ] **Step 1: Write failing tests**

```php
public function test_scan_datang_writes_hadir_for_pelajar(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'datang')
        ->set('token', 'ABC123')
        ->call('scan')
        ->assertHasNoErrors()
        ->assertSet('token', '')
        ->assertSet('pesan', $siswa->nama.' — Datang');

    $row = AbsensiPelajar::firstOrFail();
    $this->assertSame($siswa->id, (int) $row->pelajar_id);
    $this->assertSame(AbsensiStatus::HADIR, (int) $row->status);
    $this->assertNotNull($row->datang);
    $this->assertNull($row->pulang);
}

public function test_second_datang_is_rejected(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
    AbsensiPelajar::create([
        'jadwal_id' => $slot->id,
        'pelajar_id' => $siswa->id,
        'datang' => '2026-09-14 08:05:00',
        'status' => AbsensiStatus::HADIR,
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'datang')
        ->set('token', 'ABC123')
        ->call('scan')
        ->assertHasErrors(['token' => 'Sudah absen datang']);
}

public function test_unknown_token_is_rejected(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('token', 'NOPE99')
        ->call('scan')
        ->assertHasErrors(['token' => 'Nomor registrasi tidak ditemukan']);
}

public function test_pelajar_other_kelas_is_rejected(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
    $lain = Kelas::create(['nama' => 'B', 'markas_id' => $kelas->markas_id]);
    User::factory()->create([
        'role_id' => 4,
        'kelas_id' => $lain->id,
        'nomor_registrasi' => 'XYZ789',
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('token', 'XYZ789')
        ->call('scan')
        ->assertHasErrors(['token' => 'Bukan pelajar kelas ini']);
}

public function test_scan_datang_writes_hadir_for_pendidik(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
    $guru->update(['nomor_registrasi' => 'GURU01']);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'datang')
        ->set('token', 'GURU01')
        ->call('scan')
        ->assertHasNoErrors();

    $row = AbsensiPendidik::firstOrFail();
    $this->assertSame($guru->id, (int) $row->pendidik_id);
    $this->assertSame(AbsensiStatus::HADIR, (int) $row->status);
}
```

`slotFixture()`: `ownMarkasFixture()` plus `Jadwal` 2026-09-14 08:00–09:00 and pelajar `nomor_registrasi` `ABC123`, `role_id` 4, `kelas_id` = kelas A, `Pelajar` markas Genteng. Return `[$admin, $kelas, $mapel, $guru, $slot, $siswa]`.

- [ ] **Step 2: Run, confirm FAIL** (`scan` missing)

- [ ] **Step 3: Implement `scan()` datang path**

`slotAktif(): Jadwal` — `AdminVisibility::jadwalQuery(auth()->user())->whereKey($this->jadwal_id)->firstOrFail()`.

Window helper: `$now = now();` allow if `$now->between($slot->mulai->copy()->subHour(), $slot->selesai->copy()->addHour(), true)`.

Lookup `User::where('nomor_registrasi', trim($this->token))->first()`. Classify pelajar vs pendidik as spec. Datang: if existing row `datang` not null → error `Sudah absen datang`. Else `updateOrCreate` on `(jadwal_id, pelajar_id|pendidik_id)` with `datang = now()`, `status = AbsensiStatus::HADIR`. Clear `token`, set `pesan`.

View: `<form wire:submit="scan">` with `<input id="token" wire:model="token" autofocus autocomplete="off">`. No submit button. Alpine `x-init` / `$wire.on` not required if Livewire enter-submit on the form works.

- [ ] **Step 4: Run AbsensiSlotTest**

Expected: datang tests PASS.

- [ ] **Step 5: Commit**

```
feat: record datang from USB registration-number scan
```

---

### Task 4: Scan pulang, jurnal, ±1 hour window

**Files:**
- Modify: `AbsensiSlot.php` — pulang branch
- Modify: view — mode toggle, jurnal textarea in pulang mode
- Modify: tests

**Interfaces:**
- Extends `scan()` pulang + window

- [ ] **Step 1: Write failing tests**

```php
public function test_pulang_without_datang_is_rejected(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'pulang')
        ->set('token', 'ABC123')
        ->call('scan')
        ->assertHasErrors(['token' => 'Belum absen datang']);
}

public function test_guru_utama_pulang_requires_jurnal(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
    AbsensiPendidik::create([
        'jadwal_id' => $slot->id,
        'pendidik_id' => $guru->id,
        'datang' => '2026-09-14 08:00:00',
        'status' => AbsensiStatus::HADIR,
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'pulang')
        ->set('token', $guru->nomor_registrasi)
        ->set('jurnal', '')
        ->call('scan')
        ->assertHasErrors(['jurnal' => 'Jurnal wajib diisi']);

    $this->assertNull(AbsensiPendidik::first()->pulang);
}

public function test_guru_utama_pulang_with_jurnal_succeeds(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
    $guru->update(['nomor_registrasi' => 'GURU01']);
    AbsensiPendidik::create([
        'jadwal_id' => $slot->id,
        'pendidik_id' => $guru->id,
        'datang' => '2026-09-14 08:00:00',
        'status' => AbsensiStatus::HADIR,
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'pulang')
        ->set('jurnal', 'Aljabar linier')
        ->set('token', 'GURU01')
        ->call('scan')
        ->assertHasNoErrors();

    $row = AbsensiPendidik::first();
    $this->assertNotNull($row->pulang);
    $this->assertSame('Aljabar linier', $row->jurnal);
}

public function test_other_guru_pulang_without_jurnal_ok(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot] = $this->slotFixture();
    $lain = User::factory()->create(['role_id' => 3, 'nomor_registrasi' => 'GURU02']);
    Pendidik::create(['pendidik_id' => $lain->id, 'markas_id' => $kelas->markas_id]);
    AbsensiPendidik::create([
        'jadwal_id' => $slot->id,
        'pendidik_id' => $lain->id,
        'datang' => '2026-09-14 08:00:00',
        'status' => AbsensiStatus::HADIR,
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'pulang')
        ->set('jurnal', '')
        ->set('token', 'GURU02')
        ->call('scan')
        ->assertHasNoErrors();

    $this->assertNotNull(AbsensiPendidik::where('pendidik_id', $lain->id)->first()->pulang);
}

public function test_scan_outside_window_is_rejected(): void
{
    Carbon::setTestNow('2026-09-14 06:50:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('token', 'ABC123')
        ->call('scan')
        ->assertHasErrors(['token' => 'Di luar jam absensi']);
}
```

Give `guru` a `nomor_registrasi` in `slotFixture` (`GURU01`) so pulang tests do not collide.

- [ ] **Step 2: Run, confirm FAIL**

- [ ] **Step 3: Implement pulang + window**

At start of `scan()`: if outside window, `addError('token', 'Di luar jam absensi'); return;`

Pulang: no datang → `Belum absen datang`. Already pulang → `Sudah absen pulang`. If `$user->id === (int) $slot->pendidik_id` and `trim($this->jurnal) === ''` → `addError('jurnal', 'Jurnal wajib diisi'); return;`. Else set `pulang = now()`, and if guru utama set `jurnal`. Ignore `jurnal` for others.

View: buttons set `mode` to `datang`/`pulang`. `@if ($mode === 'pulang')` textarea `jurnal` with hint “Hanya dipakai saat scan guru utama”.

- [ ] **Step 4: Run AbsensiSlotTest**

Expected: PASS.

- [ ] **Step 5: Commit**

```
feat: record pulang with guru-utama jurnal and one-hour window
```

---

### Task 5: Izin / sakit / alpa form

**Files:**
- Modify: `AbsensiSlot.php` — `simpanIzin()`
- Modify: view — izin form
- Modify: tests

**Interfaces:**
- Produces: `simpanIzin(): void`

- [ ] **Step 1: Write failing tests**

```php
public function test_izin_requires_keterangan(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('izin_user_id', (string) $siswa->id)
        ->set('izin_status', (string) AbsensiStatus::IZIN)
        ->set('izin_keterangan', '')
        ->call('simpanIzin')
        ->assertHasErrors(['izin_keterangan' => 'Keterangan wajib untuk izin']);
}

public function test_sakit_without_keterangan_ok(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('izin_user_id', (string) $siswa->id)
        ->set('izin_status', (string) AbsensiStatus::SAKIT)
        ->set('izin_keterangan', '')
        ->call('simpanIzin')
        ->assertHasNoErrors();

    $row = AbsensiPelajar::first();
    $this->assertSame(AbsensiStatus::SAKIT, (int) $row->status);
    $this->assertNull($row->datang);
}

public function test_scan_overrides_izin_to_hadir(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
    AbsensiPelajar::create([
        'jadwal_id' => $slot->id,
        'pelajar_id' => $siswa->id,
        'status' => AbsensiStatus::IZIN,
        'keterangan' => 'keluarga',
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('mode', 'datang')
        ->set('token', 'ABC123')
        ->call('scan')
        ->assertHasNoErrors();

    $row = AbsensiPelajar::first();
    $this->assertSame(AbsensiStatus::HADIR, (int) $row->status);
    $this->assertNotNull($row->datang);
}

public function test_izin_rejected_when_already_hadir(): void
{
    Carbon::setTestNow('2026-09-14 08:30:00');
    [$admin, $kelas, $mapel, $guru, $slot, $siswa] = $this->slotFixture();
    AbsensiPelajar::create([
        'jadwal_id' => $slot->id,
        'pelajar_id' => $siswa->id,
        'datang' => '2026-09-14 08:05:00',
        'status' => AbsensiStatus::HADIR,
    ]);

    Livewire::actingAs($admin)
        ->test(AbsensiSlot::class)
        ->set('kelas_id', (string) $kelas->id)
        ->set('jadwal_id', (string) $slot->id)
        ->set('izin_user_id', (string) $siswa->id)
        ->set('izin_status', (string) AbsensiStatus::IZIN)
        ->set('izin_keterangan', 'urut')
        ->call('simpanIzin')
        ->assertHasErrors(['izin_user_id' => 'Sudah hadir, ubah lewat scan pulang atau biarkan']);
}
```

- [ ] **Step 2: Run, confirm FAIL**

- [ ] **Step 3: Implement `simpanIzin()`**

Validate `izin_user_id` in `pelajarForKelas` ids + `pendidikForKelas` ids. `izin_status` in `[2,3,4]`. If status is `2`, `izin_keterangan` required with copy `Keterangan wajib untuk izin`. Load existing absensi row; if `status === HADIR` and `datang` not null → error copy exactly `Sudah hadir, ubah lewat scan pulang atau biarkan`. Else `updateOrCreate` with `status`, `keterangan` (nullable), **without** setting `datang`/`pulang`. Reset izin fields. Scan datang already overwrites status 2/3/4 from Task 3.

Izin select: merge pelajarList + pendidikList. Status select: Izin/Sakit/Alpa.

- [ ] **Step 4: Run AbsensiSlotTest**

Expected: PASS.

- [ ] **Step 5: Commit**

```
feat: allow admin izin sakit and alpa on a slot
```

---

### Task 6: Sidebar, redirects, access test

**Files:**
- Modify: `resources/views/layouts/partials/admin-sidebar-nav.blade.php`
- Modify: `resources/views/admin/beranda.blade.php`
- Modify: `routes/web.php`
- Modify: `tests/Feature/Admin/AdminAccessTest.php`

**Interfaces:**
- Produces: sidebar `route('admin.absensi')`; `GET staf-admin.absensi.beranda` 302 to `admin.absensi`

- [ ] **Step 1: Write failing tests in AdminAccessTest**

Change `assertSee(route('staf-admin.absensi.beranda', absolute: false)` to `assertSee(route('admin.absensi', absolute: false)`. Add:

```php
public function test_legacy_absensi_beranda_redirects_to_admin_absensi(): void
{
    $this->actingAs($this->markasAdmin())
        ->get(route('staf-admin.absensi.beranda'))
        ->assertRedirect(route('admin.absensi'));
}

public function test_legacy_absensi_upload_redirects_and_does_not_write(): void
{
    $this->actingAs($this->markasAdmin())
        ->post(route('staf-admin.absensi.upload-absensi'), ['token' => 'ABC123'])
        ->assertRedirect(route('admin.absensi'));

    $this->assertSame(0, AbsensiPelajar::count());
}
```

Keep `Route::has('staf-admin.absensi.beranda')` and `Route::has('staf-admin.absensi.upload-absensi')`. Keep rekap route names as-is (not redirected).

- [ ] **Step 2: Run AdminAccessTest, confirm FAIL**

- [ ] **Step 3: Wire nav and redirects**

```php
Route::get('/absensi/beranda', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absensi.beranda');
Route::get('/absensi/{id}', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absen');
Route::get('/absensi-pulang/{id}', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absen-pulang');
Route::post('/absensi-pulang/selesai', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absen-pulang.selesai');
Route::post('/absensi/upload-absensi', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absensi.upload-absensi');
Route::post('/absensi/upload-absensi/izin-pelajar', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absensi.upload-izin-pelajar');
Route::post('/absensi/upload-absensi/izin-pendidik', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absensi.upload-izin-pendidik');
Route::get('/absensi/hapus/izin-pelajar/{id}', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absensi.hapus-izin-pelajar');
Route::get('/absensi/hapus/izin-pendidik/{id}', fn () => redirect()->route('admin.absensi'))->name('staf-admin.absensi.hapus-izin-pendidik');
```

Do **not** redirect `staf-admin.absensi.rekap-*` or staf-absensi (`upload-absensi.staf`, `upload-izin-staf`). Keep those names on the old controller.

Sidebar:

```php
<a href="{{ route('admin.absensi') }}" data-nav="absensi" class="ck-nav-link {{ $active('admin.absensi') }}">
```

Beranda: `route('admin.absensi')`.

`admin.absensi` must stay registered. Place `/admin/absensi` **before** any `/absensi/{id}` in the staf-admin group (already a different prefix `/staf-admin`).

- [ ] **Step 4: Run AdminAccessTest + AbsensiSlotTest + full PHPUnit**

Expected: 0 failures.

- [ ] **Step 5: Browser check**

Open `/admin/absensi` as admin. Pick a kelas that has a today slot. Confirm slot select. Do not scan production cards. Confirm sidebar Absensi is active. Confirm `/staf-admin/absensi/beranda` 302s to `/admin/absensi`.

- [ ] **Step 6: Commit**

```
feat: point admin absensi nav at the slot desk
```

---

## Spec coverage

| Spec item | Task |
| --- | --- |
| `pelajarForKelas` via `users.kelas_id` | 1 |
| `/admin/absensi`, admin-role, today slots | 2 |
| USB scan datang, token errors | 3 |
| Pulang, jurnal guru utama, ±1 hour | 4 |
| Izin/Sakit/Alpa, scan override | 5 |
| Sidebar + legacy desk redirects | 6 |
| Pendidik checklist / rekap / staf | out of scope |

## Placeholder scan

No TBD. Error copies locked in tests. Pelajar kelas column is `users.kelas_id`.
