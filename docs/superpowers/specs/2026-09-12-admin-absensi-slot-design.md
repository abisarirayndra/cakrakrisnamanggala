# Admin slot attendance (QR datang/pulang)

Date: 2026-09-12

## Goal

Replace the SB Admin absensi desk with a Cakra panel Livewire page. An admin picks one **kelas** and one **today slot from the weekly jadwal composer**, then records datang/pulang by scanning ID-card QR (`users.nomor_registrasi`) or sets Izin/Sakit/Alpa from a short form.

This spec covers **admin desk only**. Pendidik ticking names during their mapel is a later spec.

## Locked decisions

- Attendance hangs off an existing `adm_jadwal` row. No new absensi tables. No `adm_pelajars` column changes.
- Visibility is **markas via `kelas.markas_id`**, same helpers as jadwal (`kelasForJadwal`, `jadwalQuery`, `pendidikForKelas`).
- Superadmin sees every kelas that has a `markas_id`. Admin sees only assigned markas.
- Admin picks **kelas + slot first**, then scans many cards into that slot.
- Two explicit modes: **Datang** and **Pulang**. USB HID scanner types `nomor_registrasi` and sends **Enter**; the scan field stays focused, submits on Enter, clears, refocuses. No extra submit button.
- Window: now must be in `[mulai − 1 hour, selesai + 1 hour]`. Outside → reject.
- Who may be recorded on a slot: **pelajar aktif of that kelas** (`role_id` 4, `adm_pelajars.kelas_id`) **plus all pendidik of that markas** (`pendidikForKelas`). More than one guru may hadir.
- **Guru utama** = `adm_jadwal.pendidik_id`. Jurnal (`adm_absensi_pendidik.jurnal`) is required **once**, on that guru’s **pulang**. Other guru pulang without jurnal. Pelajar never have jurnal.
- Status integers on existing `status` column: `1` Hadir, `2` Izin, `3` Sakit, `4` Alpa.
- Keterangan wajib **hanya Izin**. Sakit and Alpa may have empty `keterangan`.
- Scan **overrides** Izin/Sakit/Alpa → Hadir (writes `datang` or `pulang` as the current mode).
- Hapus/ubah jadwal stays blocked when any absensi row exists for that slot (already implemented).
- Visual system: Plus Jakarta Sans, cream `#F6F3EE`, navy `#243044`, gold `#B8954A`. Livewire v3, Alpine, Bootstrap 5.3. Layout `layouts.panel-cakra`.
- PHP 8.3. PHPUnit in-memory SQLite.

## Out of scope

- Pendidik checklist / “centang nama yang masuk”.
- Webcam / phone camera scanning.
- Printing ID cards or generating QR images (cards already encode `nomor_registrasi`).
- Rekap, cetak jurnal harian, absensi staf, jasmani QR lama.
- Changing `adm_pelajars` columns.

## Current system (context)

`adm_absensi_pelajar`: `jadwal_id`, `pelajar_id`, `datang`, `pulang`, `status`, `keterangan`.

`adm_absensi_pendidik`: `jadwal_id`, `pendidik_id`, `datang`, `pulang`, `status`, `jurnal`, `keterangan`.

Old desk (`JadwalAbsensiController`) filters today’s slots by `staf_id = auth`, which hides slots created by another admin of the same markas. New desk uses `jadwalQuery` (markas), not `staf_id`.

Old scan payload field was `token` = `nomor_registrasi`. Status `1` hadir, `2` izin. Sakit/alpa were not first-class.

## HTTP

- New: `GET /admin/absensi` → Livewire `App\Livewire\Admin\AbsensiSlot`, name `admin.absensi`. Middleware: `auth` + `admin-role`. All admins.
- Sidebar Operasional **Absensi** points to `admin.absensi`. Active when `admin.absensi`.
- `GET /staf-admin/absensi/beranda` redirects to `admin.absensi`. Keep name `staf-admin.absensi.beranda` on the redirect.
- Do not wire old `staf-admin.absensi.upload-*` from the new page. Leave those names registered as redirects to `admin.absensi` so bookmarks cannot bypass the new rules (same pattern as jadwal mutators).
- Non-admin: 403.

## Slot list

Property `kelas_id` (string, empty until chosen), `jadwal_id` (string).

Slots: `jadwalQuery($actor)->where('adm_jadwal.kelas_id', $kelas_id)->whereDate('adm_jadwal.mulai', today())->orderBy('adm_jadwal.mulai')`.

Empty kelas: hint “Pilih kelas”. Empty slots: “Tidak ada mapel hari ini”. Kelas without `markas_id` omitted via `kelasForJadwal`.

Changing `kelas_id` clears `jadwal_id`, scan mode, and form.

**Authorize slot:** load via `jadwalQuery($actor)->whereKey($id)`. Missing → 404. Other markas → 403.

## Scan

Public state: `mode` = `datang` | `pulang` (default `datang`); `token` string.

`scan()`:

1. Abort unless slot authorized and now inside the ±1 hour window. Error key `token`, copy `Di luar jam absensi`.
2. Trim `token`. Empty → validation required.
3. `User::where('nomor_registrasi', $token)->first()`. Missing → `Nomor registrasi tidak ditemukan`.
4. Classify:
   - Pelajar: `role_id === 4` and `adm_pelajars.kelas_id === slot.kelas_id`. Else if role 4 but other kelas → `Bukan pelajar kelas ini`.
   - Pendidik: `role_id === 3` and user is in `pendidikForKelas($slot.kelas)`. Else → `Bukan pendidik markas ini`.
   - Other roles → `Kartu tidak untuk absensi mapel`.
5. Datang:
   - If row exists and `datang` is not null → `Sudah absen datang`.
   - Else create/update: `datang = now()`, `status = 1`. Do not set `pulang`. Scan may overwrite `status` 2/3/4.
6. Pulang:
   - No row or `datang` null → `Belum absen datang`.
   - `pulang` already set → `Sudah absen pulang`.
   - If the user is guru utama (`user.id === slot.pendidik_id`) and the `jurnal` field is empty → do not write pulang; error key `jurnal`, copy `Jurnal wajib diisi`.
   - Else set `pulang = now()`, keep `status = 1`. If guru utama, also save `jurnal`. For anyone else, ignore `jurnal`.
7. Clear `token`. Set `pesan` to `{nama} — Datang` or `{nama} — Pulang`.

One absensi row per `(jadwal_id, pelajar_id)` or `(jadwal_id, pendidik_id)`.

Clock: `now()` / `today()` use `config('app.timezone')` (Asia/Jakarta).

Pulang mode always shows textarea `jurnal` (hint: hanya dipakai saat scan guru utama).

## Izin form

Fields: `izin_user_id`, `izin_status` (`2|3|4`), `izin_keterangan`.

Validate: user in the allowed pelajar/pendidik set for the slot; `izin_status` in 2,3,4; if status is 2, `izin_keterangan` required (`Keterangan wajib untuk izin`).

Write/update the absensi row: `status` as chosen, `keterangan` as given (nullable for 3/4). Do **not** set `datang`/`pulang` for a pure izin/sakit/alpa. If the person already hadir (`status` 1 with `datang`), form is rejected: `Sudah hadir, ubah lewat scan pulang atau biarkan`.

After save, reset the form fields.

## UI

Page title `Absensi`. Pattern: filter card, then `col-lg-8` roster + `col-lg-4` sticky work card (same as jadwal after the layout tidy).

Roster: two lists (Pendidik, Pelajar). Each row: nama, badge status, `H:i` datang/pulang if set. Guru utama labelled “Guru utama”. Empty: “Belum ada absensi”.

Work card: mode toggle Datang/Pulang; scan `<input>` autofocus; jurnal textarea shown in Pulang mode (hint: hanya dipakai saat scan guru utama); izin form below.

Scan result: `alert-ck` or inline `ck-hint` on the work card, not a full-page SweetAlert.

## Tests

Extend `CreatesAdminMasterSchema` absensi stubs with `keterangan` (both) and `jurnal` (pendidik).

Cover: non-admin 403; other-markas slot 403; datang writes status 1; second datang rejected; pulang without datang rejected; guru utama pulang without jurnal rejected; other guru pulang without jurnal OK; Izin without keterangan rejected; Sakit without keterangan OK; scan overrides Izin to Hadir; outside ±1 hour rejected; unknown token rejected; pelajar other kelas rejected; sidebar `admin.absensi`; legacy beranda redirects.

## Later (not this spec)

Pendidik mapel checklist. Rekap. Staf absensi. Card printing.
