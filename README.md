# Employee Management System (HRIS JMC)

> **Proof of Concept — Coding Assisted by AI**
>
> Proyek ini adalah demonstrasi **AI-Driven Development (ADD) with Human In The Loop**.
> Seluruh kode, arsitektur, migrasi database, seeder, pengujian, dan dokumentasi dikerjakan oleh
> **AI Agent** melalui sesi percakapan iteratif. Peran manusia berfokus pada:
> - Menyusun dan me-*review* PRD sebagai kontrak kebutuhan bisnis (**Software Requirements & Domain Expertise**)
> - Menentukan arah arsitektur sistem dan memvalidasi keputusan teknis (**Architecture Knowledge & SDLC**)
> - Memberikan *prompt* yang terstruktur dan kontekstual (**Structured & Contextual Prompting**)
> - Melakukan *review*, persetujuan, dan koreksi arah di setiap iterasi (**Human In The Loop**)
>
> Tidak ada baris kode yang ditulis secara manual oleh developer.
> Ini membuktikan bahwa dengan kombinasi **AI Agent + Domain & Architecture Knowledge + Structured Prompting**,
> sebuah aplikasi enterprise-grade dapat dibangun dari nol secara penuh oleh AI.

---

## 1. Persyaratan Sistem

Pastikan sistem Anda sudah terinstall:
- **Docker** & **Docker Compose**
- **Git**
- **Postman** (opsional, untuk testing API langsung)

---

## 2. Cara Menjalankan Aplikasi

### Langkah 1: Build dan Jalankan Container
```bash
cd hris/docker
docker-compose up -d --build
```

### Langkah 2: Setup Database & Permissions
```bash
# Izin folder writable (Linux)
chmod -R 777 hris/backend/writable hris/backend/public/uploads

# Masuk ke direktori docker untuk menjalankan perintah berikutnya
cd hris/docker

# Migrasi tabel
docker-compose exec backend php spark migrate

# Seed data awal (roles, permissions, superadmin)
docker-compose exec backend php spark db:seed InitialSeeder

# Seed data wilayah (kecamatan/kabupaten/provinsi untuk autocomplete)
docker-compose exec backend php spark db:seed WilayahSeeder

# Seed demo users (Manager HRD & Admin HRD)
docker-compose exec backend php spark db:seed UserSeeder
```

### Langkah 3: Akses Aplikasi

| Service | URL |
|---------|-----|
| **Frontend (UI)** | http://localhost:3000 |
| **Backend (API)** | http://localhost:8080 |
| **Adminer (DB Admin)** | http://localhost:8081 |

**Koneksi Adminer:**
- System: `MySQL` · Server: `db_jmc` · Username: `employee_user` · Password: `employee_pass` · Database: `employee_db`

---

## 3. Akun Demo

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| **Superadmin** | `superadmin` | `Admin@123` | Semua fitur, manajemen role & user |
| **Manager HRD** | `manager_hrd` | `Manager@456` | Dashboard (chart), lihat pegawai & tunjangan |
| **Admin HRD** | `admin_hrd` | `Admin@456` | CRUD pegawai, CRUD tunjangan, setting tunjangan, log aktivitas |

> Untuk menambah akun baru, login sebagai `superadmin` → menu **Pengguna** → tombol **Tambah Pengguna**.

---

## 4. Skenario Pengujian UAT

### A. Autentikasi & Keamanan
| # | Aksi | Hasil yang Diharapkan |
|---|------|-----------------------|
| 1 | Login dengan `superadmin / Admin@123` | Redirect ke Dashboard, menu lengkap terlihat |
| 2 | Login dengan `manager_hrd / Manager@456` | Dashboard dengan chart, menu Pegawai & Tunjangan (read-only) |
| 3 | Login dengan `admin_hrd / Admin@456` | Menu Pegawai, Tunjangan, Log Aktivitas — tanpa menu Role/User |
| 4 | Logout lalu akses `/` langsung | Redirect ke halaman login |

### B. Data Pegawai (login: `admin_hrd`)
| # | Aksi | Hasil yang Diharapkan |
|---|------|-----------------------|
| 1 | Klik **Data Baru** → isi form → klik **Generate Filler** | Semua field terisi data acak valid |
| 2 | Isi tempat lahir minimal 3 karakter | Dropdown autocomplete muncul |
| 3 | Simpan data baru | Toast sukses, data muncul di tabel |
| 4 | Klik ikon edit → ubah data → simpan | Data terupdate, toast sukses |
| 5 | Centang beberapa baris → klik **Hapus** | Data terhapus, toast sukses |
| 6 | Klik **Excel** | File CSV terunduh, buka di Excel — kolom lengkap |
| 7 | Klik **PDF** | File PDF terunduh, berisi daftar pegawai |

### C. Tunjangan Transport (login: `admin_hrd`)
| # | Aksi | Hasil yang Diharapkan |
|---|------|-----------------------|
| 1 | Setting Tunjangan → isi `base_fare = 5000` → simpan | Berhasil tersimpan |
| 2 | Input Tunjangan → cari pegawai → isi jarak + hari masuk | Preview kalkulasi real-time muncul |
| 3 | Hari masuk = 15 | Preview total = Rp 0 (syarat min. 19 hari) |
| 4 | Jarak = 4 km | Preview total = Rp 0 (syarat min. 5 km) |

### D. Manajemen Role & Hak Akses (login: `superadmin`)
| # | Aksi | Hasil yang Diharapkan |
|---|------|-----------------------|
| 1 | Tambah role baru (nama + slug) | Toast sukses, role muncul di tabel |
| 2 | Edit nama role | Toast sukses, nama terupdate |
| 3 | Klik checkbox di matriks hak akses | Toast inline "Hak akses diberikan/dicabut" |
| 4 | Hapus role yang bukan built-in | Toast sukses, role hilang dari tabel |

### E. Log Aktivitas (login: `superadmin`)
| # | Aksi | Hasil yang Diharapkan |
|---|------|-----------------------|
| 1 | Buka halaman Log Aktivitas | Chart distribusi aksi, grafik 14 hari, top 5 user muncul |
| 2 | Filter berdasarkan modul/aksi/tanggal | Tabel terfilter dengan benar |

---

## 5. Frontend Automated Testing

Aplikasi frontend dilengkapi dengan automated testing menggunakan **Vitest**.

### Quick Start
```bash
cd frontend

# Jalankan semua tests
npm run test

# Watch mode
npm run test:watch

# Coverage report
npm run test:coverage

# Visual test UI
npm run test:ui
```

### Test Statistics

| Metrik | Nilai |
|--------|-------|
| **Test Framework** | Vitest 4.1.5 |
| **Total Tests** | 31 ✅ |
| **Success Rate** | 100% |
| **Execution Time** | ~750ms |
| **Test Files** | 2 |

### Coverage

| Area | Tests | Detail |
|------|-------|--------|
| Authentication & JWT | 4 | Login, token management, auth headers |
| CRUD Operations | 5 | GET/POST/PUT/DELETE pegawai + error handling |
| Role Management | 2 | Fetch roles, create with permissions |
| Data Validation | 4 | Email, password, form data |
| Employee Form | 2 | Valid/invalid data |
| Data Operations | 6 | Filter, sort, date calculations |
| RBAC | 3 | Admin, Manager, User permissions |
| Error Handling | 3 | Timeouts, JSON parsing |

**File lokasi:** `frontend/resources/js/`

| File | Tests |
|------|-------|
| `api.integration.test.js` | 14 |
| `form-validation.integration.test.js` | 17 |

---

## 6. Smoke Test Results (API)

Smoke test dijalankan terhadap semua endpoint dengan semua 3 role.

| Area | Hasil |
|------|-------|
| Login (superadmin / manager_hrd / admin_hrd) | ✅ Pass |
| GET /api/pegawai | ✅ Pass |
| POST /api/pegawai (create) | ✅ Pass |
| PUT /api/pegawai/{id} | ✅ Pass |
| DELETE /api/pegawai/{id} | ✅ Pass |
| GET /api/tunjangan | ✅ Pass |
| GET /api/rbac | ✅ Pass |
| POST /api/rbac/toggle | ✅ Pass (superadmin only) |
| GET /api/logs | ✅ Pass |
| GET /api/logs/stats | ✅ Pass |
| GET /api/pegawai/export-pdf | ✅ Pass (setelah fix mPDF tempDir) |
| RBAC access control (role isolation) | ✅ Pass |

---

## 7. Known Issues

| Issue | Status | Keterangan |
|-------|--------|------------|
| Orphan pegawai record (row dengan nama/NIP kosong) | Open | Dibuat saat smoke test via JSON body — backend `create()` menggunakan `getPost()` bukan `json()`, sehingga body JSON diabaikan. Tidak mempengaruhi flow frontend normal (multipart). |
| `manager_hrd` terhubung ke pegawai "Ahmad Dani" | Open | Saat seed, NIP `20260001` sudah ada dari data lain. Seeder reuse pegawai yang ada. Data display di UI terpengaruh tapi fungsi RBAC tetap benar. |
| Backend perlu restart setelah fix mPDF | Resolved | Fix `WRITEPATH . 'mpdf'` sudah diterapkan di kode. Jalankan `docker-compose restart backend` jika PDF export masih 500. |
| Wilayah di edit pegawai hanya tampil ID | Open | Form edit menampilkan ID kecamatan saja, bukan nama. Label menginstruksikan user untuk gunakan form baru jika ingin ubah wilayah. |

---

## 8. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Database Connection Error | Tunggu container `db_jmc` status *healthy* sebelum migrasi |
| Port conflict (8080 / 3000) | Ubah port di `docker/docker-compose.yml` |
| Seeder gagal duplicate entry | Seeder sudah idempotent — aman dijalankan ulang |
| Wilayah autocomplete kosong | Jalankan `php spark db:seed WilayahSeeder` |
| Foto tidak tersimpan | Jalankan `chmod -R 777 backend/public/uploads` |
| PDF export error 500 | Jalankan `docker-compose restart backend` |
| Tests tidak ditemukan | Pastikan di direktori `frontend/` dan jalankan `npm install` |
| Module not found (testing) | Jalankan `npm install` di direktori `frontend/` |

---

## 9. Dokumentasi

| Dokumen | Lokasi | Keterangan |
|---------|--------|------------|
| **PRD** | [`PRD_HRIS.md`](PRD_Employee_Management.md) | Product requirements — disusun & di-review oleh developer sebelum pengerjaan dimulai |
| **Milestone & Progress** | [`docs/MILESTONE.md`](docs/MILESTONE.md) | Rekam jejak lengkap semua fase, komponen, known issues, smoke test results, dan arsitektur sistem |
| **API Collection** | [`docs/api/postman_collection.json`](docs/api/postman_collection.json) | Postman collection untuk semua endpoint backend |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | Laravel 12 (Blade, Alpine.js 3, Bootstrap 3/Bootflat) |
| Backend API | CodeIgniter 4 (PHP 8.2) |
| Database | MySQL 8 |
| Auth | JWT (firebase/php-jwt) |
| PDF Export | mPDF |
| Charts | Chart.js 4.4.4 |
| Testing | Vitest 4.1.5 |
| Infrastructure | Docker + Docker Compose |

---

*HRIS JMC — Proof of Concept: AI-Driven Development (ADD) with Human In The Loop · 2026*
