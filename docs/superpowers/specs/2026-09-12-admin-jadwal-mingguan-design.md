# Weekly class schedule composer

Date: 2026-09-12

## Goal

Replace the SB Admin jadwal screen with a Cakra panel Livewire composer. An admin picks one **kelas** and one **calendar week (Monday–Sunday)** and fills teaching slots for that week. Each slot is a real `adm_jadwal` row with dated `mulai` / `selesai`, so existing absensi can attach later.

This spec covers **jadwal composition only**. Absensi (QR, datang/pulang, jurnal, rekap) stays on the old SB Admin routes until a later spec.

## Locked decisions

- Visibility is **markas via `kelas.markas_id`**, not `adm_jadwal.staf_id`.
- Superadmin sees every kelas that has a `markas_id`. Admin sees only kelas in `markasIds()`.
- Any admin who can see a kelas may add / edit / hapus its slots that week.
- Compose one kelas at a time. No “copy last week”. No all-kelas grid.
- Keep existing `adm_jadwal` columns. Do not add a template table. Do not change `adm_pelajars` columns.
- `staf_id` on create = the signed-in user (so the old absensi filter `where staf_id = auth id` still works for the creator until absensi is modernized).
- Hapus and edit are blocked when the slot already has `adm_absensi_pelajar` or `adm_absensi_pendidik` rows.
- Kelas without `markas_id` are omitted from the composer (no markas → no pendidik list).
- Visual system: Plus Jakarta Sans, cream `#F6F3EE`, navy `#243044`, gold `#B8954A`. Livewire v3, Alpine, Bootstrap 5.3. Layout `layouts.panel-cakra`.
- PHP 8.3. PHPUnit in-memory SQLite.

## Out of scope

- Presensi mulai/selesai mapel, QR, izin, jurnal, rekap.
- Changing how old `/staf-admin/absensi*` pages query `staf_id`.
- Recurring templates, holidays, room assignment.
- Pelajar or pendidik-facing timetable pages.

## Current system (context)

Table `adm_jadwal`: `staf_id`, `mapel_id`, `pendidik_id`, `kelas_id`, `mulai`, `selesai`, timestamps.

Old UI (`JadwalAbsensiController::index`): filter kelas + month + year; add a slot with datetime-local. Scope used `staf_id = auth` and markas from `adm_pendidik` on the admin — broken for admins who are not also a pendidik row.

Old absensi beranda still lists today’s slots where `staf_id = auth`. New composer must keep writing `staf_id` so that flow does not go empty for the person who created the slot.

## HTTP

- New: `GET /admin/jadwal` → Livewire `App\Livewire\Admin\JadwalMingguan`, name `admin.jadwal`. Middleware: `auth` + `admin-role`. All admins (not super-only).
- Sidebar Operasional **Jadwal** points to `admin.jadwal`. Active when `admin.jadwal`.
- `GET /staf-admin/jadwal` redirects to `admin.jadwal` (keep name `staf-admin.jadwal` on the redirect so old `Route::has` tests and bookmarks work).
- Leave `staf-admin.jadwal.tambah|edit|update|hapus` in place for now (dead UI). Do not wire them from the new page.

Non-admin: 403 on `admin.jadwal`.

## Week model

- Property `senin` is `Y-m-d` of the week’s Monday.
- Default: Monday of the current week (`Carbon::now()->startOfWeek(Carbon::MONDAY)`).
- If the user types another date, snap to that date’s Monday.
- Week range: `senin` 00:00 through `senin + 6 days` 23:59:59.
- Day labels (id): Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu.

## Query helpers (`AdminVisibility`)

```php
public static function kelasForJadwal(User $actor): Builder
```

Kelas with non-null `markas_id`, `orderBy('nama')`. Super: all such kelas. Admin: `whereIn('markas_id', $actor->markasIds())` (empty ids → no rows).

```php
public static function jadwalQuery(User $actor): Builder
```

`Jadwal` joined to `kelas`. Super: no markas filter. Admin: `whereIn('kelas.markas_id', $actor->markasIds())`. Select `adm_jadwal.*`.

```php
public static function pendidikForKelas(Kelas $kelas): Builder
```

Users `role_id` 3 joined `adm_pendidik` where `adm_pendidik.markas_id = $kelas->markas_id`, `orderBy('users.nama')`.

## Livewire `JadwalMingguan`

Layout `layouts.panel-cakra`, title `Jadwal`.

`boot()`: `abort_unless(auth()->user()?->isAdmin(), 403)`.

Public state:

- `kelas_id` string (empty until chosen)
- `senin` string `Y-m-d`
- `hari` string `0`–`6` (offset from Monday, default `0`)
- `mapel_id`, `pendidik_id` strings
- `jam_mulai`, `jam_selesai` strings `H:i`
- `editId` `?int`

When `kelas_id` or `senin` changes: reset `editId` and the add-form fields except the filters; stay on the same week/kelas.

**Authorize a row:** load `Jadwal` with `kelas`. 404 if missing. If actor is not super, 403 unless `kelas.markas_id` is in `markasIds()`.

**List:** if `kelas_id` empty, show the filters and a hint “Pilih kelas”. Else `jadwalQuery($actor)->where('kelas_id', $kelas_id)->whereBetween('mulai', [$start, $end])->with(['mapel','pendidik','kelas'])->orderBy('mulai')`. Group in the view by calendar day.

**Tambah / simpan** (same form, like Master Markas):

Validate:

- `kelas_id` required, exists `kelas,id`, and the kelas is in `kelasForJadwal($actor)`
- `hari` required, integer 0–6
- `mapel_id` required, exists `mapels,id`
- `pendidik_id` required, exists `users,id`, and is in `pendidikForKelas($kelas)`
- `jam_mulai` / `jam_selesai` required, `date_format:H:i`, `selesai` after `mulai`
- Combined datetimes must fall on `senin + hari`
- Reject overlap: another slot for the same `kelas_id` where `mulai < $selesai && selesai > $mulai`, ignoring `editId`. Message: `Jam bentrok dengan slot lain`

On create: `staf_id = auth()->id()`. On update: do not change `staf_id`.

If `editId` set and the row has absensi: error `Jadwal sudah dipakai absensi` and do not update.

**Ubah:** fill the form from the row (hari from `mulai`, times `H:i`). Heading “Ubah slot” / submit “Simpan perubahan” / Batal.

**Hapus:** authorize; if absensi exists, `addError('hapus', 'Jadwal sudah dipakai absensi')`; else delete. If `editId` was that id, `batal()`.

## UI

One card: filters (kelas select, date `senin` with hint “Minggu Senin–Minggu”), then seven day sections. Empty day: “Tidak ada slot”. Each slot: mapel, nama pendidik, `HH:mm–HH:mm`, Ubah, Hapus (`wire:confirm`).

Right or below: form Tambah slot / Ubah slot — hari, mapel, pendidik, jam mulai, jam selesai.

Pendidik options come from `pendidikForKelas` of the selected kelas. If no kelas, disable the form.

## Tests

PHPUnit + Livewire, `CreatesAdminMasterSchema` expanded so `adm_jadwal` has the real columns and stub `adm_absensi_pelajar` / `adm_absensi_pendidik` exist.

Cover: non-admin 403; admin only own-markas kelas; super sees other markas kelas; list only that week; create writes datetimes and `staf_id`; overlap rejected; other-markas 403; hapus blocked when absensi exists; sidebar uses `admin.jadwal`.

## Later (not this spec)

Absensi per slot: datang at mapel start, pulang at mapel end, scoped by markas like this composer so any markas admin can run the session, not only `staf_id`.
