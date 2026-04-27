# HRIS JMC — Milestone Pengerjaan

> **Proyek:** Human Resource Information System — JMC  
> **Stack:** CodeIgniter 4 (Backend API) + Laravel 12 (Frontend) + Docker  
> **Pendekatan:** AI-Driven Development (ADD) with Human In The Loop  
> **Tanggal mulai:** 2026-04-27  
> **Tanggal selesai:** 2026-04-27

---

## Ringkasan Fase Pengerjaan

Pengerjaan mengikuti 8 fase yang ditetapkan pada PRD dan Implementation Plan:

| Fase | Ruang Lingkup | Status |
|------|--------------|--------|
| Phase 1 | Foundation & Security (RBAC Core, ApiService, Middleware) | ✅ Selesai |
| Phase 2 | Auth Module (JWT, Captcha, Remember Me) | ✅ Selesai |
| Phase 3 | Employee Management (CRUD, Autocomplete, Foto, Export) | ✅ Selesai |
| Phase 4 | Tunjangan Transport (Kalkulasi, Eligibility, Setting) | ✅ Selesai |
| Phase 5 | User Management & Role Permission Matrix | ✅ Selesai |
| Phase 6 | Dashboard (Role-based view, Chart.js) | ✅ Selesai |
| Phase 7 | Activity Logs (Audit Trail, Filter, Stats Charts) | ✅ Selesai |
| Phase 8 | Final Validation (Smoke Test, Bug Fix, Hardening) | ✅ Selesai |

---

## Milestone 1 — Backend Foundation (CI4 REST API)
**Status:** ✅ Selesai

| Komponen | Keterangan | Status |
|----------|------------|--------|
| JWT Authentication | Login multi-identifier (username / email / no_hp), generate & refresh token | ✅ |
| RBAC System | Role (superadmin, admin_hrd, manager_hrd), permission matrix `modul:aksi` | ✅ |
| Force Logout Queue | Toggle status user → paksa logout sesi aktif | ✅ |
| Pegawai CRUD | Create, Read (paginate + sort + filter), Update, Delete, Bulk Delete | ✅ |
| Pendidikan (relasi) | Dynamic list pendidikan per pegawai | ✅ |
| Wilayah API | Autocomplete kecamatan → auto-fill kabupaten & provinsi | ✅ |
| Foto Upload | Upload PNG/JPG < 2MB, simpan di `public/uploads/foto/` | ✅ |
| Tunjangan Transport | Input, kalkulasi (formula + rounding), eligibility rules | ✅ |
| Setting Tunjangan | Base fare history, berlaku dari | ✅ |
| User Management | CRUD user, link ke pegawai, auto-generate password | ✅ |
| Activity Log | Audit trail: login, logout, CRUD per modul | ✅ |
| Dashboard Stats | Widget total, distribusi jabatan & gender, 5 pegawai terbaru, per departemen | ✅ |
| Rate Limiting | Throttle per IP pada endpoint login | ✅ |
| Export PDF | List pegawai ke PDF via mPDF | ✅ |
| `.htaccess` | Rewrite rules untuk routing CI4 | ✅ |

---

## Milestone 2 — Docker Infrastructure
**Status:** ✅ Selesai

| Komponen | Keterangan | Status |
|----------|------------|--------|
| `Dockerfile.backend` | PHP 8.3 + Apache, CI4 backend, `www-data` user | ✅ |
| `Dockerfile.frontend` | PHP 8.3 + Apache, Laravel 12 frontend | ✅ |
| `docker-compose.yml` | Services: frontend (`:3000`), backend (`:8080`), db_jmc, adminer (`:8081`) | ✅ |
| `apache_frontend.conf` | DocumentRoot `/var/www/html/public` — fix path mismatch volume mount | ✅ |
| Volume mount | `../frontend:/var/www/html` → Laravel terbaca di container | ✅ |
| SQLite permissions | `database.sqlite` chown `www-data` untuk session & cache | ✅ |
| Network internal | Backend dapat diakses frontend via hostname `backend` | ✅ |

---

## Milestone 3 — Laravel 12 Frontend — Core Layer
**Status:** ✅ Selesai

| Komponen | File | Status |
|----------|------|--------|
| ApiService | `app/Services/ApiService.php` | ✅ |
| Auto Bearer Token | Inject `Authorization: Bearer {token}` dari session tiap request | ✅ |
| ApiAuthMiddleware | Force logout check via `GET /api/auth/me` tiap request | ✅ |
| Middleware alias | `api.auth` didaftarkan di `bootstrap/app.php` | ✅ |
| Routes | `routes/web.php` — guest, authenticated, API proxy wilayah | ✅ |
| `.env` | `API_BASE_URL=http://backend` | ✅ |
| Guzzle `http_errors: false` | 4xx/5xx tidak throw exception, dihandle di controller | ✅ |

---

## Milestone 4 — Laravel 12 Frontend — Controllers
**Status:** ✅ Selesai

| Controller | Fungsi utama | Status |
|------------|-------------|--------|
| `AuthController` | showLogin, login (captcha math, remember_me), logout | ✅ |
| `DashboardController` | index → `/api/dashboard`, pass `role` & `dashData` ke view | ✅ |
| `PegawaiController` | index, create, store, show, edit, update, destroy, bulkDelete, exportPdf, **exportExcel** | ✅ |
| `UserController` | index, store, update, toggleStatus, destroy, autocomplete (AJAX) | ✅ |
| `TunjanganController` | index, store, calculatePreview (AJAX), setting, storeSetting | ✅ |
| `ActivityLogController` | index dengan filter (modul, aksi, from, to, q, page), **stats charts** | ✅ |
| `RoleController` | index (roles + matrix), store, **update**, destroy, **togglePermission** | ✅ |

---

## Milestone 5 — Laravel 12 Frontend — UI (Bootflat)
**Status:** ✅ Selesai

> Seluruh UI dimigrasi dari Tailwind CDN ke **Bootflat 2.0.4** (Bootstrap 3.4 flat UI kit).  
> Asset lokal di `public/bootflat/` — tidak ada CDN Tailwind tersisa.

### Asset & Layout

| File | Keterangan | Status |
|------|------------|--------|
| `public/bootflat/` | Copy dari `/bootflat/bootflat/` — css, js, img | ✅ |
| `layouts/app.blade.php` | Sidebar fixed 220px, top navbar, Bootstrap 3 panels, iCheck, Alpine.js, **global toast system** | ✅ |

### Views

| View | Fitur utama | Status |
|------|-------------|--------|
| `login.blade.php` | Form BS3, icon FA, password strength bar, math captcha, remember me | ✅ |
| `dashboard.blade.php` | Widget cards + bar chart departemen + doughnut (jabatan & gender) + tabel 5 staf terbaru (Manager only); welcome banner (semua role) | ✅ |
| `pegawai/index.blade.php` | Tabel, sortable header, filter, bulk delete, pagination, **tombol Excel & PDF** | ✅ |
| `pegawai/create.blade.php` | Panel sections, Alpine.js autocomplete, dynamic education list, **Generate Filler button** | ✅ |
| `pegawai/edit.blade.php` | Sama seperti create, data pre-filled, **foto preview 96px circle** | ✅ |
| `pegawai/show.blade.php` | Profile header gradient, detail grid, list riwayat pendidikan | ✅ |
| `users/index.blade.php` | Tabel, BS3 modal + Alpine.js pegawai autocomplete, toggle status, delete | ✅ |
| `tunjangan/index.blade.php` | Tabel, BS3 modal + Alpine.js calculation preview real-time, **validasi JS pre-submit** | ✅ |
| `tunjangan/setting.blade.php` | Alert setting aktif, form tambah setting, tabel riwayat | ✅ |
| `logs/index.blade.php` | Filter bar, tabel dengan BS3 label badges, pagination, **3 Chart.js panels (doughnut aksi, line 14 hari, horizontal bar top-5 user)** | ✅ |
| `roles/index.blade.php` | Tabel roles, **AJAX permission matrix toggle**, edit modal, modal tambah role | ✅ |

### Frontend JavaScript Libraries

| Library | Versi | Sumber | Kegunaan |
|---------|-------|--------|----------|
| Bootstrap 3 JS | 3.4.1 | CDN | Modal, dropdown, dismiss |
| jQuery | 3.7.1 | CDN | Bootstrap dependency, AJAX |
| iCheck | — | Lokal | Styled checkbox/radio (Bootflat) |
| Alpine.js | 3.14.8 | CDN | Autocomplete, dynamic list, calc preview |
| Chart.js | 4.4.4 | CDN | Doughnut, bar, line, horizontal bar charts |

---

## Milestone 6 — Validasi & Keamanan
**Status:** ✅ Selesai

| Validasi | Detail | Status |
|----------|--------|--------|
| NIP | Hanya digit, min 8 karakter, onkeyup strip non-digit | ✅ |
| Nama | Hanya huruf + tanda petik (`'`), validasi onkeyup | ✅ |
| No. HP | Format `+62xxxxxxxxx`, auto-prefix, feedback real-time | ✅ |
| Password strength | Bar 4 level (lemah/cukup/kuat/sangat kuat), onkeyup | ✅ |
| Math Captcha | Dua angka random disimpan session, user isi jawaban | ✅ |
| RBAC di view | `canDo($permissions, 'modul', 'aksi')` untuk sembunyikan tombol | ✅ |
| RBAC di backend | `$isSuperadmin` bypass semua, non-superadmin cek permission | ✅ |
| CSRF | `@csrf` di semua form, `X-CSRF-TOKEN` di AJAX fetch | ✅ |
| Rate limiting | Endpoint login CI4 dibatasi per IP | ✅ |
| Session flush | Logout / force logout → `Session::flush()` + redirect | ✅ |

---

## Milestone 7 — Enhancements, Fixes & Data Seeding
**Status:** ✅ Selesai

Penyempurnaan iteratif berdasarkan hasil smoke test dan review PRD:

| Item | Detail | Status |
|------|--------|--------|
| Toast notification system | Menggantikan BS3 flash alert — global `showToast()`, auto-dismiss 5 detik, 4 variant | ✅ |
| Export Excel (CSV UTF-8) | Download CSV dengan BOM, header kolom lengkap, respects active filter | ✅ |
| Permission matrix AJAX toggle | Superadmin edit hak akses langsung via checkbox — `POST /api/rbac/toggle`, revert on fail | ✅ |
| Edit role modal | Modal edit role dengan validasi JS pre-submit (nama & slug wajib) | ✅ |
| Dashboard bar chart (departemen) | Panel full-width `Pegawai per Departemen` hanya untuk Manager HRD | ✅ |
| Dashboard superadmin/admin | Hapus counter stats sesuai PRD 12.1 & 12.3 — cukup welcome banner | ✅ |
| Log stats charts | 3 chart: distribusi aksi (doughnut), aktivitas 14 hari (line), top 5 user (bar horizontal) | ✅ |
| Generate Filler (pegawai create) | Tombol isi otomatis semua field dengan data acak valid termasuk Alpine.js reactive fields | ✅ |
| Foto preview edit view | Tampilan foto aktif 96px circle, preview overlay saat pilih file baru | ✅ |
| Foto upload fix (backend create) | `create()` CI4 kini handle multipart foto setelah insert | ✅ |
| mPDF tempDir fix | `WRITEPATH . 'mpdf'` — hindari permission denied di Docker | ✅ |
| `WilayahSeeder` | 8 provinsi, 20 kabupaten, 58 kecamatan kota-kota besar Indonesia | ✅ |
| `UserSeeder` | Demo user: `manager_hrd` (Manager@456) & `admin_hrd` (Admin@456) — idempotent | ✅ |
| Permissions modul fix | DB: `modul='log'` → `modul='activity_log'` agar admin_hrd bisa akses log | ✅ |

---

## Milestone 8 — Automated Testing & Smoke Test
**Status:** ✅ Selesai

### Frontend Automated Testing (Vitest)

Dikerjakan mandiri oleh developer sebagai lapisan pengujian tambahan:

| Metrik | Nilai |
|--------|-------|
| Framework | Vitest 4.1.5 |
| Total Tests | 31 ✅ |
| Success Rate | 100% |
| Test Files | `api.integration.test.js` (14), `form-validation.integration.test.js` (17) |

Area: Authentication & JWT, CRUD Operations, Role Management, Data Validation, Employee Form, Data Operations, RBAC, Error Handling.

### API Smoke Test (All Roles)

| Endpoint / Skenario | superadmin | manager_hrd | admin_hrd |
|--------------------|:----------:|:-----------:|:---------:|
| Login | ✅ | ✅ | ✅ |
| GET /api/pegawai | ✅ | ✅ | ✅ |
| POST /api/pegawai | ✅ | ✅ (blocked) | ✅ |
| PUT /api/pegawai/{id} | ✅ | ✅ (blocked) | ✅ |
| DELETE /api/pegawai/{id} | ✅ | ✅ (blocked) | ✅ |
| GET /api/tunjangan | ✅ | ✅ | ✅ |
| GET /api/rbac | ✅ | — | — |
| POST /api/rbac/toggle | ✅ | ✅ (blocked) | ✅ (blocked) |
| GET /api/logs | ✅ | — | ✅ |
| GET /api/logs/stats | ✅ | — | ✅ |
| GET /api/pegawai/export-pdf | ✅ | — | ✅ |

> "blocked" = endpoint mengembalikan 403 sesuai RBAC — **expected behavior**.

---

## Known Issues (Non-Blocking)

| Issue | Dampak | Keterangan |
|-------|--------|------------|
| Orphan pegawai record | Kosmetik | Row dengan nama/NIP kosong dari smoke test via JSON body — tidak mempengaruhi flow frontend normal |
| `manager_hrd` linked ke pegawai lain | Data | NIP seed sudah ada di DB saat UserSeeder pertama kali dijalankan — fungsi RBAC tetap benar |
| Wilayah di edit pegawai hanya tampil ID | UX | Form edit menampilkan kecamatan_id, bukan nama; label menginstruksikan user untuk form baru |

---

## Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                   Docker Compose                     │
│                                                     │
│  ┌──────────────┐    ┌──────────────┐    ┌───────┐  │
│  │  Frontend    │    │   Backend    │    │  DB   │  │
│  │  Laravel 12  │───▶│ CodeIgniter 4│───▶│MySQL 8│  │
│  │  :3000       │    │  REST API    │    │       │  │
│  │  Blade/Alpine│    │  :8080       │    └───────┘  │
│  └──────────────┘    └──────────────┘               │
│                             │                       │
│                      ┌──────────────┐               │
│                      │   Adminer    │               │
│                      │   :8081      │               │
│                      └──────────────┘               │
└─────────────────────────────────────────────────────┘
```

**Auth Flow:** Browser → Laravel (session) → CI4 API (JWT) → MySQL  
**RBAC:** `permissions` table (`role_id`, `modul`, `aksi`) — dievaluasi di backend per request  
**Frontend:** Thin client, tidak ada logika bisnis — semua kalkulasi di backend  

---

*Terakhir diperbarui: 2026-04-27 — Proyek selesai*
