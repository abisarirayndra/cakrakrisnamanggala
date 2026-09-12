# Admin role merge and master data

Date: 2026-09-05

## Goal

Merge **admin** (`role_id` 2) and **staf admin** (`role_id` 7) into one admin role. Super access is a flag, not a second role. Visible master data follows the admin’s assigned markas. Superadmin can later assign several markas to one admin without another schema change.

This spec covers identity, markas scoping, the admin panel shell, and three Livewire master pages: admin, pelajar, pendidik.

## Locked decisions

- Visibility is **markas**, not `created_by`.
- Master data **admin** is **superadmin only**.
- Approach: pivot `admin_markas` from day one (one row per admin now; later config only adds/removes rows).
- CAT/dinas package routes stay **superadmin only** (staf never had them).
- `adm_pelajars` columns and business fields do not change.
- Visual system: Plus Jakarta Sans, cream `#F6F3EE`, navy `#243044`, gold `#B8954A`. Livewire v3, Alpine, Bootstrap 5.3.
- PHP 8.3. Default new pendidik password: `pendidik123`.
- Out of this spec: absensi, jadwal, pendaftar approval modal.

## Roles after merge

| `users.role_id` | Meaning | Login destination |
| --- | --- | --- |
| 1 | Unused Super | 403 (unchanged) |
| 2 | Admin (all staff) | `admin.beranda` |
| 3 | Pendidik | unchanged |
| 4 | Pelajar | unchanged |
| 5 | Pendaftar | unchanged |
| 6 | Suspended pelajar | unchanged |
| 7 | Removed | migrated to 2 |

`users.is_super_admin` is a boolean, default `false`.

- Existing `role_id` 2 → `is_super_admin = true`.
- Existing `role_id` 7 → `role_id = 2`, `is_super_admin = false`.
- `User::isAdmin()` stays `role_id === 2`.
- `User::isSuperAdmin()` reads the **flag**, not `role_id === 1`.
- `User::isStafAdmin()` becomes an alias of `isAdmin()` only if old call sites still need it; new code uses `isAdmin()` / `isSuperAdmin()`.
- `User::dashboardRouteName()` maps 2 (and leftover 7 until migrated) to `admin.beranda`.

## Markas assignment

Table `admin_markas`:

- `id`
- `user_id` (FK users)
- `markas_id` (FK `adm_markas`)
- unique `(user_id, markas_id)`

Rules:

- Superadmin: pivot rows optional. Queries **do not** filter by markas.
- Non-super admin: at least one pivot row is required at create time. Queries filter with `whereIn(markas_id, $user->markasIds())`.
- `User::markasIds(): array<int>` returns assigned markas ids (empty for superadmin is fine because callers skip the filter).
- `User::scopeVisibleTo(User $actor)` (or a dedicated query helper) applies the filter for pelajar/pendidik lists.

Backfill: for each migrated role-7 user, if `adm_pendidik.markas_id` is set, insert that pair into `admin_markas`. Admins with no markas see empty pelajar/pendidik lists until a superadmin assigns markas.

Pendidik and pelajar/pendaftar keep using existing `adm_pendidik.markas_id` and `adm_pelajars.markas_id`. Do not store those on `admin_markas`.

Later multi-markas config is a superadmin UI on the same pivot. Do not build that UI in this spec beyond the add-admin form allowing **one** markas (a select). The table already allows many rows.

## HTTP access

- One panel prefix: `/admin`, middleware `auth` + `admin-role` (`isAdmin()`).
- Super-only routes (master admin, CAT/dinas that already live under `/admin`): extra middleware `superadmin-role` (`isSuperAdmin()`). Forbidden → **403**.
- Non-super must not reach master admin by URL even if the menu is hidden.
- `/staf-admin/*` redirects to the matching `/admin` URL (or `admin.beranda` when there is no match) so old bookmarks work.
- `POST /log` and Livewire login send every `role_id` 2 user to `admin.beranda`.

## Admin shell

New layout (not SB Admin): navy topbar, cream page, gold primary actions. Same tokens as guest pendaftaran.

**Beranda** is a simple landing for the signed-in admin (name + role label Superadmin / Admin). Do not rebuild the old Highcharts administrasi dashboard in this spec.

Navigation:

- Superadmin: Beranda, Admin, Pelajar, Pendidik, plus existing CAT/dinas links that already sit in the admin group.
- Non-super: Beranda, Pelajar, Pendidik only.

## Master Admin (super only)

Livewire list: search (nama/email), pagination.

Create:

- `nama`, `email` (unique), `password`
- `is_super_admin` checkbox
- `markas_id` select from `adm_markas`: **required if not super**, optional if super
- Creates `users` with `role_id = 2` and a pivot row when markas is present

Actions: view, delete. Cannot delete the signed-in user.

Non-super: 403 on these routes.

## Master Pelajar

Livewire list: search (nama/email/`nomor_registrasi`), pagination.

Scope:

- Super: all `role_id` 4
- Non-super: join `adm_pelajars` where `markas_id` in `markasIds()`
- Pelajar with null `markas_id`: super only

Actions:

- View and edit existing `adm_pelajars` / `users` fields already used on the old form. No new pelajar columns.
- Suspend (`role_id` 6) and delete: **superadmin only**. Non-super UI omits those actions; URLs 403.

Pendaftar queue and migrasi modal are **out of scope**.

## Master Pendidik

Livewire list: search (nama/email), pagination.

Scope:

- Super: all `role_id` 3
- Non-super: `adm_pendidik.markas_id` in `markasIds()`

Create:

- `nama`, `email` (unique), `markas_id`
- Non-super: markas select limited to their assigned markas (if only one, preselected)
- Password stored as hash of `pendidik123` (no `is_active` column, no aktif toggle)
- `users.role_id = 3` plus `adm_pendidik` with that `markas_id` (keep existing `mapel_id` default `10` used today)

Actions: view, delete.

## Validation and errors

Reuse existing Indonesian copy where it already exists (email unique, required fields). New copy only when there is no legacy string (e.g. markas wajib untuk admin non-super). Unauthorized: 403.

## Tests

PHPUnit, SQLite in-memory, PHP 8.3 with `pdo_sqlite` / `sqlite3`, same pattern as `WizardPendaftaranTest` / `FormLoginTest`.

Must cover:

1. Super sees pelajar/pendidik from every markas; non-super sees only assigned markas.
2. Non-super 403 on master admin and on a CAT/dinas admin route.
3. Creating a non-super admin without markas fails; creating a superadmin without markas succeeds.
4. Creating a pendidik stores password `pendidik123`.
5. Cannot delete own admin account.
6. Login for `role_id` 2 (flag on or off) redirects to `admin.beranda`.

## Out of scope

- Absensi, jadwal, rekap, QR
- Livewire modal terima pendaftar + assign kelas
- Multi-markas assignment UI beyond a single select on create admin / create pendidik
- Restyling reset password
- Changing `adm_pelajars` schema
