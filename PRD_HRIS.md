# PRD — Aplikasi Pengelolaan Data Pegawai (Employee Management System)

**Version:** 1.0.0  
**Last Updated:** 2026-04-25  
**Status:** Draft — Ready for Agent Implementation  
**Owner:** JMC Indonesia  
**Target:** Junior Programmer Technical Test

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Architecture](#2-architecture)
   - [2.1 Overview](#21-overview)
   - [2.2 Separation of Concerns](#22-separation-of-concerns)
   - [2.3 Communication Protocol](#23-communication-protocol)
3. [Tech Stack](#3-tech-stack)
   - [3.1 Backend](#31-backend)
   - [3.2 Frontend](#32-frontend)
   - [3.3 Development Tools](#33-development-tools)
4. [Project Structure](#4-project-structure)
5. [Authentication & JWT](#5-authentication--jwt)
6. [RBAC Matrix](#6-rbac-matrix)
7. [Database Schema](#7-database-schema)
8. [API Specification](#8-api-specification)
9. [Module: Login](#9-module-login)
10. [Module: Kelola Role](#10-module-kelola-role)
11. [Module: Kelola User](#11-module-kelola-user)
12. [Module: Dashboard](#12-module-dashboard)
13. [Module: Data Pegawai](#13-module-data-pegawai)
14. [Module: Setting Tunjangan Transport](#14-module-setting-tunjangan-transport)
15. [Module: Tunjangan Transport](#15-module-tunjangan-transport)
16. [Module: Log](#16-module-log)
17. [Frontend Laravel Convention](#17-frontend-laravel-convention)
18. [Docker & Environment Setup](#18-docker--environment-setup)
19. [API Documentation](#19-api-documentation)
20. [Testing Requirements](#20-testing-requirements)
21. [Assumptions & Decisions](#21-assumptions--decisions)

---

## 1. Project Overview

### 1.1 Description

Aplikasi web untuk pengelolaan data pegawai perusahaan yang dikembangkan menggunakan Laravel 12 sebagai frontend engine yang berinteraksi dengan REST API backend. UI menggunakan pola "Island" untuk komponen interaktif yang kompleks.

### 1.2 Key Objectives

- Membangun sistem RBAC (Role-Based Access Control) yang ketat
- Menyediakan REST API yang lengkap dengan autentikasi JWT
- Memisahkan frontend dan backend secara bersih (decoupled architecture)
- Mendukung operasi CRUD lengkap dengan API endpoints (GET, POST, PUT, DELETE)
- Seluruh aplikasi berjalan di Docker container

### 1.3 Timeline

Pengerjaan maksimal **72 jam (3 hari kalender)** sejak waktu start dinyatakan.

---

## 2. Architecture

### 2.1 Overview

```
┌───────────────────────────────────────────────────────────┐
│                        CLIENT BROWSER                      │
│            (Vue Components / jQuery for Interactivity)     │
└──────────────────────────┬────────────────────────────────┘
                           │ HTTP/HTTPS (HTML/JSON)
                    ┌──────▼──────┐
                    │   FRONTEND  │
                    │  Laravel 12 │
                    │ (Blade/BFF) │
                    └──────┬──────┘
                           │ Guzzle API Client
                           │ Authorization: Bearer <JWT>
                    ┌──────▼──────┐
                    │   BACKEND   │
                    │ CodeIgniter4│
                    │  REST API   │
                    │  (JWT Auth) │
                    └──────┬──────┘
                           │
                    ┌──────▼──────┐
                    │   MariaDB   │
                    │  (Latest)   │
                    └─────────────┘
```

### 2.2 Separation of Concerns

| Layer      | Responsibility                                                         |
|------------|------------------------------------------------------------------------|
| Frontend   | UI rendering (Blade), State Management (Session), API calls (Guzzle)   |
| Backend    | Business logic, data validation, RBAC enforcement, JWT issuance        |
| Database   | Data persistence, referential integrity, stored procedures             |

### 2.3 Communication Protocol

- Semua komunikasi frontend ↔ backend via **JSON REST API**
- Autentikasi menggunakan **JWT Bearer Token** di header `Authorization`
- Token disimpan di Laravel Session pada frontend

---

## 3. Tech Stack

### 3.1 Backend

| Komponen       | Teknologi                   | Versi       |
|----------------|-----------------------------|-------------|
| Language       | PHP                         | Latest (8.x)|
| Framework      | CodeIgniter 4               | Latest CI4  |
| Database       | MariaDB                     | Latest      |
| Web Server     | Apache 2.x                  | Latest      |
| Auth           | JWT (firebase/php-jwt)      | Latest      |
| Container      | Docker                      | Latest      |

### 3.2 Frontend

| Komponen       | Teknologi                         | Keterangan                               |
|----------------|-----------------------------------|------------------------------------------|
| Core           | Laravel 12                        | Routing, Controllers, Blade Rendering    |
| UI Pattern      | Vue Island Pattern                | Vue.js komponen di dalam Blade           |
| CSS Framework  | Basecoat UI (Vanilla CSS)         | Disesuaikan dari legacy themes           |
| Markup         | Blade Templates                   |                                          |
| Styling        | Vanilla CSS                       |                                          |
| Charts         | Chart.js                          |                                          |

> **PENTING:** Arsitektur frontend menggunakan Laravel 12 dengan pola Vue Island yang diintegrasikan ke dalam template Blade untuk interaktivitas komponen tertentu.

### 3.3 Development Tools

| Tool        | Kegunaan                                        |
|-------------|-------------------------------------------------|
| Docker      | Container runtime                               |
| Composer    | PHP dependency manager                          |
| npm/yarn    | Frontend dependency manager                     |
| Postman     | API documentation & Testing                     |
| PHPUnit     | Unit testing (backend)                          |
| Git         | Version control (repo disediakan perusahaan)    |

---

## 4. Project Structure

### 4.1 Repository Layout

```
project-root/
├── backend/                    # CodeIgniter 4 application
│   ├── app/
│   │   ├── Config/
│   │   │   ├── Routes.php
│   │   │   └── Jwt.php         # JWT config
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── RoleController.php
│   │   │       ├── UserController.php
│   │   │       ├── PegawaiController.php
│   │   │       ├── TunjanganController.php
│   │   │       ├── SettingTunjanganController.php
│   │   │       └── LogController.php
│   │   ├── Filters/
│   │   │   ├── JwtFilter.php   # JWT validation filter
│   │   │   └── RbacFilter.php  # Role permission filter
│   │   ├── Models/
│   │   ├── Database/
│   │   │   └── Migrations/     # WAJIB: semua tabel via migration
│   │   └── Libraries/
│   │       └── JwtLibrary.php
│   ├── tests/                  # PHPUnit tests
│   ├── .env
│   └── composer.json
│
├── frontend/                   # Laravel 12 Frontend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   └── Middleware/
│   │   └── Services/           # ApiService (Guzzle wrapper)
│   ├── bootstrap/
│   ├── config/
│   ├── public/
│   ├── resources/
│   │   ├── css/
│   │   ├── js/
│   │   │   └── components/     # Vue Islands
│   │   └── views/              # Blade Templates
│   ├── routes/
│   │   └── web.php
│   ├── .env
│   ├── composer.json
│   └── package.json
│
├── docker/
│   ├── docker-compose.yml
│   ├── Dockerfile.backend
│   ├── Dockerfile.frontend
│   └── mariadb/
│       └── init.sql
│
├── docs/
│   ├── api/                    # API spec
│   │   └── postman_collection.json
│   ├── PRD.md                  # This file
│   └── TESTING.md
│
└── README.md                   # Setup & environment guide
```

---

## 5. Authentication & JWT

### 5.1 JWT Flow

```
1. Client POST /login (Laravel) -> hits /api/auth/login (Backend)
2. Backend validates → issues { access_token, refresh_token, expires_in }
3. Laravel menyimpan token di Session
4. Setiap request ke backend menyertakan header: Authorization: Bearer <access_token> via ApiService (Guzzle)
5. Backend validasi JWT di JwtFilter sebelum controller dieksekusi
6. Jika token expired → Laravel auto-refresh menggunakan refresh token (jika diimplementasikan)
7. Logout (Laravel) → Laravel hits POST /api/auth/logout (Backend) & flush session
```

### 5.2 Token Specs

| Parameter       | Value                                      |
|-----------------|--------------------------------------------|
| Algorithm       | HS256                                      |
| Access Token TTL | 60 menit (kecuali remember_me aktif)      |
| Refresh Token TTL | 7 hari (30 hari jika remember_me aktif) |
| Storage         | Laravel Session (frontend)                 |
| Header Key      | `Authorization: Bearer <token>`            |

### 5.3 JWT Payload

```json
{
  "iss": "jmc-employee-app",
  "iat": 1714000000,
  "exp": 1714003600,
  "sub": "<user_id>",
  "user": {
    "id": 1,
    "username": "johndoe",
    "nama": "John Doe",
    "role_id": 2,
    "role_nama": "Manager HRD"
  }
}
```

### 5.4 Remember Me Logic

- Jika `remember_me = true`: refresh_token TTL 30 hari, access_token auto-refresh di background
- Jika `remember_me = false`: session expired setelah access_token habis, tidak ada auto-refresh

### 5.5 Force Logout

Ketika admin mengubah status user menjadi **nonaktif**, server harus:
1. Menyimpan `user_id` yang di-nonaktifkan ke tabel `force_logout_queue`
2. Setiap request yang masuk, JwtFilter memeriksa tabel tersebut
3. Jika `user_id` ada di queue → return `401 Unauthorized` dengan pesan force logout
4. Hapus dari queue setelah token dinyatakan invalid

---

## 6. RBAC Matrix

### 6.1 Role Definitions

| ID | Role Slug      | Nama Tampil   |
|----|----------------|---------------|
| 1  | superadmin     | Superadmin    |
| 2  | manager_hrd    | Manager HRD   |
| 3  | admin_hrd      | Admin HRD     |

### 6.2 Permission Matrix

| Modul / Aktivitas          | Superadmin                  | Manager HRD     | Admin HRD                              |
|----------------------------|-----------------------------|-----------------|----------------------------------------|
| Login / Logout             | ✅                          | ✅              | ✅                                     |
| Kelola Role                | CRUD                        | ❌              | ❌                                     |
| Kelola User                | CRUD (kecuali hapus diri)   | RO + UO (own)   | RO + UO (own)                          |
| Dashboard                  | Welcome message             | Full widget     | Welcome message                        |
| Modul Data Pegawai         | ❌                          | R (all)         | CRUD (kecuali hapus pegawai superadmin)|
| Modul Tunjangan Transport  | ❌                          | RO (own)        | RO (own)                               |
| Setting Tunjangan Transport| ❌                          | ❌              | CRUD                                   |
| Modul Log                  | R (all)                     | ❌              | ❌                                     |

### 6.3 Legend

| Kode | Arti                                                              |
|------|-------------------------------------------------------------------|
| CRUD | Create, Read, Update, Delete                                      |
| R    | Read semua data                                                   |
| RO   | Read Only — hanya data milik dirinya / diperuntukkan dirinya      |
| UO   | Update Only — hanya bisa update data milik dirinya                |
| ❌   | Tidak ada akses ke modul (route tidak tersedia, UI tidak tampil)  |
| ✅   | Akses tanpa perlu aksi CRUD                                       |

### 6.4 RBAC Enforcement

- **Backend:** Setiap API endpoint dilindungi `RbacFilter`. Filter membaca role dari JWT payload dan mencocokkan dengan permission table di database.
- **Frontend:** Menu dan tombol ditampilkan/disembunyikan berdasarkan role yang tersimpan di JWT. Ini hanya UI layer — enforcement sesungguhnya ada di backend.

---

## 7. Database Schema

> **WAJIB:** Semua tabel dibuat melalui **Migration CI4**. Dilarang membuat tabel langsung via GUI (Adminer, HeidiSQL, dll).

### 7.1 Tabel `roles`

```sql
CREATE TABLE roles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug        VARCHAR(50)   NOT NULL UNIQUE,           -- e.g. superadmin, manager_hrd
  nama        VARCHAR(100)  NOT NULL,
  deskripsi   TEXT          NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at  DATETIME      NULL
);
```

### 7.2 Tabel `permissions`

```sql
CREATE TABLE permissions (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  modul       VARCHAR(100)  NOT NULL,                  -- e.g. pegawai, tunjangan
  aksi        VARCHAR(50)   NOT NULL,                  -- create, read, update, delete
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### 7.3 Tabel `role_permissions`

```sql
CREATE TABLE role_permissions (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id       INT UNSIGNED NOT NULL,
  permission_id INT UNSIGNED NOT NULL,
  FOREIGN KEY (role_id)       REFERENCES roles(id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
  UNIQUE KEY uq_role_perm (role_id, permission_id)
);
```

### 7.4 Tabel `pegawai`

```sql
CREATE TABLE pegawai (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nip             VARCHAR(20)   NOT NULL UNIQUE,
  nama            VARCHAR(150)  NOT NULL,
  email           VARCHAR(150)  NOT NULL UNIQUE,
  no_hp           VARCHAR(20)   NOT NULL,              -- format internasional: +628xxx
  foto            VARCHAR(255)  NULL,                  -- path file relatif
  tempat_lahir    VARCHAR(100)  NOT NULL,
  tanggal_lahir   DATE          NOT NULL,
  status_kawin    ENUM('kawin','tidak kawin') NOT NULL DEFAULT 'tidak kawin',
  jumlah_anak     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  tanggal_masuk   DATE          NOT NULL,
  jabatan         ENUM('Manager','Staf','Magang') NOT NULL,
  departemen      ENUM('Marketing','HRD','Production','Executive','Commissioner') NOT NULL,
  kecamatan_id    INT UNSIGNED  NULL,
  kabupaten_id    INT UNSIGNED  NULL,
  provinsi_id     INT UNSIGNED  NULL,
  alamat_lengkap  TEXT          NULL,
  status          TINYINT(1)    NOT NULL DEFAULT 1,    -- 1=aktif, 0=nonaktif
  created_by      INT UNSIGNED  NULL,
  updated_by      INT UNSIGNED  NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at      DATETIME      NULL,                  -- soft delete
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 7.5 Tabel `pegawai_pendidikan`

```sql
CREATE TABLE pegawai_pendidikan (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pegawai_id  INT UNSIGNED NOT NULL,
  jenjang     VARCHAR(50)  NOT NULL,                   -- SD, SMP, SMA, S1, S2, dll
  urutan      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (pegawai_id) REFERENCES pegawai(id) ON DELETE CASCADE
);
```

### 7.6 Tabel `users`

```sql
CREATE TABLE users (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pegawai_id      INT UNSIGNED  NOT NULL UNIQUE,
  role_id         INT UNSIGNED  NOT NULL,
  username        VARCHAR(50)   NOT NULL UNIQUE,        -- 6+ chars, lowercase, no space, alphanumeric
  email           VARCHAR(150)  NOT NULL UNIQUE,
  no_hp           VARCHAR(20)   NULL,
  password        VARCHAR(255)  NOT NULL,               -- bcrypt hashed
  status          TINYINT(1)    NOT NULL DEFAULT 1,
  created_by      INT UNSIGNED  NULL,
  updated_by      INT UNSIGNED  NULL,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at      DATETIME      NULL,
  FOREIGN KEY (pegawai_id) REFERENCES pegawai(id),
  FOREIGN KEY (role_id)    REFERENCES roles(id),
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 7.7 Tabel `token_blacklist`

```sql
CREATE TABLE token_blacklist (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  jti         VARCHAR(255) NOT NULL UNIQUE,             -- JWT ID claim
  expired_at  DATETIME     NOT NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### 7.8 Tabel `force_logout_queue`

```sql
CREATE TABLE force_logout_queue (
  user_id     INT UNSIGNED NOT NULL PRIMARY KEY,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 7.9 Tabel `setting_tunjangan_transport`

```sql
CREATE TABLE setting_tunjangan_transport (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  base_fare   DECIMAL(12,2) NOT NULL,                  -- tarif per km
  berlaku_dari DATE          NOT NULL,
  keterangan  TEXT          NULL,
  created_by  INT UNSIGNED  NULL,
  updated_by  INT UNSIGNED  NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### 7.10 Tabel `tunjangan_transport`

```sql
CREATE TABLE tunjangan_transport (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pegawai_id      INT UNSIGNED   NOT NULL,
  periode_bulan   TINYINT(2)     NOT NULL,              -- 1-12
  periode_tahun   SMALLINT(4)    NOT NULL,
  jarak_km        DECIMAL(8,2)   NOT NULL,              -- raw input jarak
  jarak_km_dibulatkan TINYINT UNSIGNED NOT NULL,        -- hasil pembulatan
  hari_masuk      TINYINT UNSIGNED NOT NULL,
  base_fare       DECIMAL(12,2)  NOT NULL,              -- snapshot saat input
  total_tunjangan DECIMAL(14,2)  NOT NULL,              -- hasil kalkulasi
  catatan         TEXT           NULL,
  created_by      INT UNSIGNED   NULL,
  updated_by      INT UNSIGNED   NULL,
  created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_pegawai_periode (pegawai_id, periode_bulan, periode_tahun),
  FOREIGN KEY (pegawai_id)  REFERENCES pegawai(id),
  FOREIGN KEY (created_by)  REFERENCES users(id) ON DELETE SET NULL
);
```

### 7.11 Tabel `activity_log`

```sql
CREATE TABLE activity_log (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED  NULL,
  username    VARCHAR(50)   NULL,                       -- snapshot username
  role        VARCHAR(50)   NULL,                       -- snapshot role
  modul       VARCHAR(100)  NOT NULL,
  aksi        VARCHAR(50)   NOT NULL,                   -- login, logout, create, read, update, delete
  deskripsi   TEXT          NULL,
  ip_address  VARCHAR(45)   NULL,
  user_agent  TEXT          NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### 7.12 Tabel Wilayah (Referensi)

```sql
-- Seed dari data wilayah Indonesia
CREATE TABLE provinsi (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama    VARCHAR(100) NOT NULL
);

CREATE TABLE kabupaten (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  provinsi_id INT UNSIGNED NOT NULL,
  nama        VARCHAR(100) NOT NULL,
  FOREIGN KEY (provinsi_id) REFERENCES provinsi(id)
);

CREATE TABLE kecamatan (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kabupaten_id  INT UNSIGNED NOT NULL,
  nama          VARCHAR(100) NOT NULL,
  FOREIGN KEY (kabupaten_id) REFERENCES kabupaten(id)
);
```

---

## 8. API Specification

### 8.1 Base URL

```
Backend  : http://localhost:8080/api
Frontend : http://localhost:3000
```

### 8.2 Global Request Headers

```
Content-Type:  application/json
Accept:        application/json
Authorization: Bearer <access_token>   (semua endpoint kecuali /auth/login)
```

### 8.3 Global Response Format

**Success:**
```json
{
  "status": true,
  "message": "Berhasil",
  "data": { ... },
  "meta": {
    "page": 1,
    "per_page": 10,
    "total": 100
  }
}
```

**Error:**
```json
{
  "status": false,
  "message": "Pesan error",
  "errors": {
    "field_name": ["Pesan validasi"]
  }
}
```

### 8.4 Endpoint List

#### AUTH

| Method | Endpoint             | Deskripsi              | Auth Required |
|--------|----------------------|------------------------|---------------|
| POST   | /auth/login          | Login & dapatkan token | ❌            |
| POST   | /auth/logout         | Logout & blacklist token | ✅          |
| POST   | /auth/refresh        | Refresh access token   | ❌ (gunakan refresh_token) |
| GET    | /auth/me             | Data user yang login   | ✅            |
| PUT    | /auth/profile        | Update profil sendiri  | ✅            |
| PUT    | /auth/change-password| Ganti password sendiri | ✅            |

#### ROLES

| Method | Endpoint       | Deskripsi              | Role Allowed  |
|--------|----------------|------------------------|---------------|
| GET    | /roles         | List semua role        | Superadmin    |
| POST   | /roles         | Buat role baru         | Superadmin    |
| GET    | /roles/{id}    | Detail role            | Superadmin    |
| PUT    | /roles/{id}    | Update role            | Superadmin    |
| DELETE | /roles/{id}    | Hapus role             | Superadmin    |
| GET    | /permissions   | List semua permission  | Superadmin    |
| PUT    | /roles/{id}/permissions | Update permission role | Superadmin |

#### USERS

| Method | Endpoint             | Deskripsi                       | Role Allowed                  |
|--------|----------------------|---------------------------------|-------------------------------|
| GET    | /users               | List semua user                 | Superadmin, Manager HRD*, Admin HRD* |
| POST   | /users               | Buat user baru (auto-gen password) | Superadmin                 |
| GET    | /users/{id}          | Detail user                     | Superadmin, Manager HRD (own), Admin HRD (own) |
| PUT    | /users/{id}          | Update user                     | Superadmin, Manager HRD (own), Admin HRD (own) |
| DELETE | /users/{id}          | Hapus user                      | Superadmin (kecuali diri sendiri) |
| PUT    | /users/{id}/status   | Toggle status aktif/nonaktif    | Superadmin                    |

> *Manager HRD dan Admin HRD hanya bisa READ dan UPDATE data diri sendiri (RO+UO).

#### PEGAWAI

| Method | Endpoint                      | Deskripsi                       | Role Allowed       |
|--------|-------------------------------|---------------------------------|--------------------|
| GET    | /pegawai                      | List pegawai (pagination, filter, search) | Manager HRD, Admin HRD |
| POST   | /pegawai                      | Tambah pegawai                  | Admin HRD          |
| GET    | /pegawai/{id}                 | Detail pegawai                  | Manager HRD, Admin HRD |
| PUT    | /pegawai/{id}                 | Update pegawai                  | Admin HRD          |
| DELETE | /pegawai/{id}                 | Soft delete pegawai             | Admin HRD (kecuali pegawai superadmin) |
| PUT    | /pegawai/{id}/status          | Update status aktif/nonaktif    | Admin HRD          |
| GET    | /pegawai/{id}/download-pdf    | Download PDF profil pegawai     | Manager HRD, Admin HRD |
| GET    | /pegawai/export/pdf           | Export list pegawai (PDF)       | Manager HRD, Admin HRD |
| GET    | /pegawai/export/excel         | Export list pegawai (Excel)     | Manager HRD, Admin HRD |
| POST   | /pegawai/{id}/foto            | Upload foto pegawai             | Admin HRD          |
| GET    | /pegawai/autocomplete         | Autocomplete nama pegawai (untuk kelola user) | Superadmin |

#### TUNJANGAN TRANSPORT

| Method | Endpoint                    | Deskripsi                        | Role Allowed |
|--------|-----------------------------|----------------------------------|--------------|
| GET    | /tunjangan                  | List tunjangan                   | Manager HRD (own), Admin HRD (own) |
| POST   | /tunjangan                  | Input tunjangan transport        | Admin HRD    |
| GET    | /tunjangan/{id}             | Detail tunjangan                 | Manager HRD (own), Admin HRD (own) |
| PUT    | /tunjangan/{id}             | Update tunjangan                 | Admin HRD    |
| DELETE | /tunjangan/{id}             | Hapus tunjangan                  | Admin HRD    |

#### SETTING TUNJANGAN TRANSPORT

| Method | Endpoint                  | Deskripsi                       | Role Allowed |
|--------|---------------------------|---------------------------------|--------------|
| GET    | /setting-tunjangan        | List setting                    | Admin HRD    |
| POST   | /setting-tunjangan        | Buat setting baru               | Admin HRD    |
| GET    | /setting-tunjangan/{id}   | Detail setting                  | Admin HRD    |
| PUT    | /setting-tunjangan/{id}   | Update setting                  | Admin HRD    |
| DELETE | /setting-tunjangan/{id}   | Hapus setting                   | Admin HRD    |
| GET    | /setting-tunjangan/aktif  | Ambil setting yang aktif/terbaru| Admin HRD    |

#### DASHBOARD

| Method | Endpoint              | Deskripsi                       | Role Allowed  |
|--------|-----------------------|---------------------------------|---------------|
| GET    | /dashboard/manager    | Data widget Manager HRD         | Manager HRD   |

#### LOG

| Method | Endpoint    | Deskripsi                              | Role Allowed |
|--------|-------------|----------------------------------------|--------------|
| GET    | /logs       | List log (pagination, filter by modul, tanggal) | Superadmin |

#### WILAYAH (Referensi)

| Method | Endpoint                              | Deskripsi                |
|--------|---------------------------------------|--------------------------|
| GET    | /wilayah/kecamatan?q={keyword}        | Autocomplete kecamatan (min 3 char) |
| GET    | /wilayah/kabupaten/{kecamatan_id}     | Kabupaten dari kecamatan |
| GET    | /wilayah/provinsi/{kabupaten_id}      | Provinsi dari kabupaten  |

---

## 9. Module: Login

### 9.1 UI Fields

| Field           | Type           | Validasi                                                      |
|-----------------|----------------|---------------------------------------------------------------|
| Identifier      | Input text     | Bisa username, email, atau no.hp (cellphone). Required.       |
| Password        | Input password | Required. Min 8 char, 1 huruf besar, 1 huruf kecil, 1 simbol |
| Captcha Code    | Input text     | Harus sama dengan captcha yang ditampilkan. Required.         |
| Remember Me     | Checkbox       | Jika dicentang, token berlaku selama 30 hari.                 |

### 9.2 Business Rules

- Mendukung pencarian user berdasarkan `username`, `email`, atau `no_hp`.
- JWT Token TTL dinamis: 1 jam (standar) atau 30 hari (remember me).
- Validasi input dilakukan secara **onkeyup** di sisi frontend.

### 9.4 API Request / Response

**Request:** `POST /api/auth/login`
```json
{
  "identifier": "johndoe",
  "password": "P@ssw0rd!",
  "captcha": "X4K9",
  "remember_me": true
}
```

**Response:**
```json
{
  "status": true,
  "message": "Login berhasil",
  "data": {
    "access_token": "<jwt>",
    "refresh_token": "<token>",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "nama": "John Doe",
      "role": "Manager HRD",
      "role_slug": "manager_hrd"
    }
  }
}
```

---

## 10. Module: Kelola Role

### 10.1 Access: Superadmin Only

### 10.2 Features

- List semua role beserta jumlah user per role
- Tambah role baru
- Edit role (nama, deskripsi)
- Hapus role (hanya jika tidak ada user yang menggunakan role tersebut)
- Assign / unassign permissions ke role (checklist per modul+aksi)

### 10.3 Business Rules

- Role default (superadmin, manager_hrd, admin_hrd) tidak boleh dihapus
- Perubahan permission langsung berlaku pada request API berikutnya (karena RBAC di-check di filter, bukan di JWT)

---

## 11. Module: Kelola User

### 11.1 Access

| Aksi                | Superadmin | Manager HRD | Admin HRD |
|---------------------|------------|-------------|-----------|
| Lihat list user     | ✅ (semua) | ✅ (diri sendiri) | ✅ (diri sendiri) |
| Tambah user baru    | ✅         | ❌          | ❌        |
| Edit user           | ✅ (semua) | ✅ (diri sendiri) | ✅ (diri sendiri) |
| Hapus user          | ✅ (kecuali diri sendiri) | ❌ | ❌       |
| Toggle status aktif | ✅         | ❌          | ❌        |

### 11.2 Form Fields

| Field            | Validasi / Keterangan                                                           |
|------------------|---------------------------------------------------------------------------------|
| Nama Pengguna    | Autocomplete dari data pegawai. Min 2 char untuk trigger suggest. Required.     |
| Username         | Min 6 char, alphanumeric, lowercase, no spasi. Unik. Validasi onkeyup.          |
| Password         | Auto-generate saat buat baru. Aturan: min 8 char, 1 huruf besar, 1 kecil, 1 simbol |
| Ulangi Password  | Harus sama dengan password. Validasi onkeyup.                                   |
| Role             | Dropdown dari tabel roles                                                       |
| Status           | Checkbox "Aktif". Default: checked.                                             |

### 11.3 Business Rules

- Password di-generate otomatis oleh sistem saat pembuatan user baru
- Password dikirim ke email pegawai (atau ditampilkan sekali di modal setelah create)
- User bisa ganti password sendiri di halaman profil
- Jika user sedang login dan statusnya diubah nonaktif → force logout via `force_logout_queue`
- Superadmin tidak bisa menghapus akun dirinya sendiri

---

## 12. Module: Dashboard

### 12.1 Superadmin Dashboard

Tampilkan hanya teks:
```
Selamat Datang, {Nama Pengguna} - {Role}
```

### 12.2 Manager HRD Dashboard

**Widgets (Cards):**

| Widget         | Isi                            | API Endpoint         |
|----------------|--------------------------------|----------------------|
| Total Pegawai  | Count semua pegawai aktif      | GET /dashboard/manager |
| Pegawai Kontrak| Count pegawai jabatan Staf     | (same response)      |
| Pegawai Tetap  | Count pegawai jabatan Manager  | (same response)      |
| Peserta Magang | Count pegawai jabatan Magang   | (same response)      |

> **Asumsi:** "Pegawai Tetap" = jabatan Manager, "Pegawai Kontrak" = jabatan Staf, "Magang" = jabatan Magang. Jika ada field jenis_pegawai yang berbeda dari jabatan, tambahkan sebagai asumsi di ASSUMPTIONS.md.

**Charts:**
- Doughnut Chart 1: Staf vs Manager vs Magang (proporsi)
- Doughnut Chart 2: Jenis Kelamin Pria vs Wanita

> **Asumsi:** Field `jenis_kelamin` perlu ditambahkan ke tabel `pegawai` sebagai ENUM('L','P').

**Tabel:**
- 5 pegawai kontrak (jabatan = Staf) dengan `tanggal_masuk` paling baru
- Kolom: Nama, NIP, Jabatan, Tanggal Masuk

### 12.3 Admin HRD Dashboard

Tampilkan hanya teks:
```
Selamat Datang, {Nama Pengguna} - {Role}
```

---

## 13. Module: Data Pegawai

### 13.1 List Pegawai

**Kolom Tabel:**

| Kolom       | Sortable | Keterangan                           |
|-------------|----------|--------------------------------------|
| No. Urut    | ❌       | Nomor urut tampil (bukan ID)         |
| NIP         | ✅       |                                      |
| Nama        | ✅       |                                      |
| Jabatan     | ✅       |                                      |
| Tanggal Masuk| ✅      |                                      |
| Masa Kerja  | ✅       | Dihitung otomatis dari tanggal masuk |
| Aksi        | ❌       | Tombol Detail, Edit, Download PDF    |

**Fitur Tabel:**
- Sorting per kolom
- Pagination (default 10 per halaman)
- Bulk select (checkbox per baris)

**Tombol-tombol:**
- `Data Baru` → halaman form tambah
- `Download PDF` → export list ke PDF
- `Download Excel` → export list ke Excel
- `Hapus Data` → hapus semua yang di-bulk select
- `Status` (dropdown: Aktif / Nonaktif) → bulk update status

**Search:** Parameter: nama, NIP, jabatan. Debounce 300ms.

**Filter:**
- Jabatan: multi-select dropdown (Manager, Staf, Magang)
- Masa Kerja: operator dropdown (`>`, `=`, `<`) + input number (dalam satuan tahun)

### 13.2 Form Tambah / Edit Pegawai

| Field             | Type                    | Validasi                                                              |
|-------------------|-------------------------|-----------------------------------------------------------------------|
| Foto              | File upload             | PNG/JPEG/JPG only. Max 2MB (asumsi). Preview setelah upload.         |
| NIP               | Input text              | Min 8 char, angka only, no spasi. Unik.                              |
| Nama Pegawai      | Input text              | Huruf, angka, tanda petik (`'`), spasi. Required.                    |
| Email             | Input email             | Format email valid. Unik.                                             |
| No. HP            | Input text              | Format internasional (+62xxx). Required.                             |
| Jenis Kelamin     | Radio button            | L / P (asumsi tambahan field)                                        |
| Tempat Lahir      | Dropdown autocomplete   | Min 3 char → muncul suggest kabupaten/kota.                          |
| Tanggal Lahir     | Date input              | Format DD/MM/YYYY. Required.                                          |
| Status Kawin      | Radio button            | Kawin / Tidak Kawin                                                  |
| Jumlah Anak       | Input number            | Max 2 digit. Default 0. Required.                                    |
| Tanggal Masuk     | Date input              | Format DD/MM/YYYY. Required.                                          |
| Jabatan           | Dropdown                | Manager / Staf / Magang                                              |
| Departemen        | Dropdown                | Marketing / HRD / Production / Executive / Commissioner              |
| Usia              | Input text (disabled)   | Auto-hitung dari `tanggal_lahir` saat input. Tidak di-submit ke API. |
| Kecamatan         | Autocomplete            | Min 3 char. Pilih dari daftar.                                       |
| Kabupaten         | Input text (disabled)   | Auto-fill setelah kecamatan dipilih.                                 |
| Provinsi          | Input text (disabled)   | Auto-fill setelah kecamatan dipilih.                                 |
| Alamat Lengkap    | Textarea                |                                                                       |
| Pendidikan        | Dynamic form list       | Bisa tambah/hapus baris. Setiap baris = jenjang pendidikan.          |
| Status            | Toggle/Checkbox         | Aktif / Nonaktif                                                     |

### 13.3 Masa Kerja Calculation

```
masa_kerja = floor( (today - tanggal_masuk) / 365.25 ) tahun
```

Tampilkan dalam format: `X tahun Y bulan` pada detail pegawai.

### 13.4 Business Rules

- Admin HRD tidak bisa menghapus pegawai yang memiliki user dengan role superadmin
- Soft delete: set `deleted_at`, record tidak dihapus dari database
- Export PDF / Excel menggunakan data yang sedang difilter/disearch

---

## 14. Module: Setting Tunjangan Transport

### 14.1 Access: Admin HRD Only

### 14.2 Fields

| Field        | Type         | Validasi                                      |
|--------------|--------------|-----------------------------------------------|
| Base Fare    | Input decimal| Format currency. Required. > 0.               |
| Berlaku Dari | Date         | Tanggal mulai berlaku setting ini              |
| Keterangan   | Textarea     | Opsional                                       |

### 14.3 Business Rules

- Bisa ada beberapa record setting (histori)
- Setting aktif = setting dengan `berlaku_dari` terbaru yang tidak melebihi tanggal hari ini
- Endpoint `GET /setting-tunjangan/aktif` mengembalikan setting yang sedang berlaku

---

## 15. Module: Tunjangan Transport

### 15.1 Access

| Aksi   | Manager HRD            | Admin HRD              |
|--------|------------------------|------------------------|
| Read   | RO (tunjangan sendiri) | RO (tunjangan sendiri) |
| CRUD   | ❌                     | ✅ (bisa input untuk semua pegawai tetap) |

### 15.2 Aturan Tunjangan

```
Tunjangan Transport = base_fare × jarak_km_dibulatkan × hari_masuk
```

**Aturan Pembulatan KM:**
- Desimal < 0.5 → bulatkan ke bawah (`floor`)
- Desimal >= 0.5 → bulatkan ke atas (`ceil`)

**Aturan Kelayakan:**
- Tunjangan hanya diberikan ke **pegawai tetap** (jabatan = Manager atau Staf — sesuai asumsi)
- Minimal hari masuk kerja: **19 hari**. Jika < 19 hari → tidak mendapat tunjangan
- Jarak maksimal yang dihitung: **25 km** (kelebihan tidak dihitung)
- Jarak minimal: **> 5 km** (jarak ≤ 5 km tidak mendapat tunjangan)

**Contoh Kalkulasi:**
```
base_fare       = Rp 2.500
jarak_raw       = 12.5 km → dibulatkan = 13 km
hari_masuk      = 22 hari (>= 19, eligible)
jabatan         = Staf (pegawai tetap, eligible)

total_tunjangan = 2.500 × 13 × 22 = Rp 715.000
```

### 15.3 Form Fields

| Field          | Type         | Keterangan                                                    |
|----------------|--------------|---------------------------------------------------------------|
| Pegawai        | Autocomplete | Hanya tampil pegawai tetap aktif                             |
| Periode Bulan  | Dropdown     | Bulan 1-12                                                    |
| Periode Tahun  | Input year   | Tahun 4 digit                                                 |
| Jarak (km)     | Input decimal| Input jarak rumah ke kantor. Sistem auto-bulatkan.           |
| Hari Masuk     | Input number | Jumlah hari masuk kerja di bulan berjalan                    |
| Total Tunjangan| Disabled     | Auto-kalkulasi. Tampilkan preview realtime saat input.        |
| Catatan        | Textarea     | Opsional                                                      |

---

## 16. Module: Log

### 16.1 Access: Superadmin Only

### 16.2 Yang Dicatat

| Event        | Modul         | Aksi                    |
|--------------|---------------|-------------------------|
| User login   | auth          | login                   |
| User logout  | auth          | logout                  |
| Buka halaman | <nama modul>  | read                    |
| Tambah data  | <nama modul>  | create                  |
| Edit data    | <nama modul>  | update                  |
| Hapus data   | <nama modul>  | delete                  |

### 16.3 Data yang Disimpan

```json
{
  "user_id": 5,
  "username": "johndoe",
  "role": "Manager HRD",
  "modul": "pegawai",
  "aksi": "read",
  "deskripsi": "Membuka list pegawai",
  "ip_address": "192.168.1.100",
  "user_agent": "Mozilla/5.0 ...",
  "created_at": "2026-04-25 10:30:00"
}
```

### 16.4 Implementasi

Log ditulis melalui **CI4 After Filter** agar tidak mencemari controller logic. Buat `LogFilter` yang di-register sebagai after filter di `Routes.php` untuk semua route `/api/*`.

### 16.5 UI

- Tabel dengan kolom: Tanggal, Username, Role, Modul, Aksi, Deskripsi, IP
- Filter: Modul (dropdown), Aksi (dropdown), Tanggal (date range picker)
- Pagination: 25 per halaman (default)

---

## 17. Frontend Laravel Convention

### 17.1 Laravel Vue Island Pattern

- Setiap halaman utama di-render menggunakan **Laravel Blade**.
- Komponen interaktif yang kompleks diimplementasikan sebagai **Vue.js Components** yang di-mount ke elemen spesifik di dalam Blade (Island Pattern).
- Komunikasi ke Backend API utama dilakukan melalui **ApiService.php** (Server-side Guzzle) untuk pengambilan data awal.
- Interaksi client-side (AJAX) menggunakan **Fetch API** atau **Axios** dengan menyertakan token CSRF Laravel jika diperlukan.
- State global (JWT) dikelola oleh Laravel Session.

### 17.2 Api Service (Server-side)

Gunakan `ApiService.php` untuk membungkus request Guzzle:

```php
// app/Services/ApiService.php
class ApiService {
    public function request($method, $endpoint, $options = []) {
        // Otomatis menyertakan Bearer Token dari Session
        // Handle token expired & refresh logic
    }
}
```

### 17.3 Route Guards

Proteksi rute menggunakan Laravel Middleware:

```php
// app/Http/Middleware/ApiAuthMiddleware.php
public function handle($request, Closure $next) {
    if (!Session::has('access_token')) {
        return redirect('/login');
    }
    return $next($request);
}
```

### 17.4 Validasi Onkeyup

Implementasikan validasi realtime (onkeyup/oninput) untuk field:
- Username (format + ketersediaan via API debounce 500ms)
- Password (strength rules)
- Ulangi Password (match check)
- NIP (format angka)
- No. HP (format internasional)

---

## 18. Docker & Environment Setup

### 18.1 `docker-compose.yml`

```yaml
version: '3.9'

services:
  backend:
    build:
      context: ./backend
      dockerfile: ../docker/Dockerfile.backend
    ports:
      - "8080:80"
    environment:
      - CI_ENVIRONMENT=development
      - database.default.hostname=db
      - database.default.database=employee_db
      - database.default.username=employee_user
      - database.default.password=employee_pass
      - JWT_SECRET=your_super_secret_key_change_in_production
    volumes:
      - ./backend:/var/www/html
    depends_on:
      - db

  frontend:
    build:
      context: ./frontend
      dockerfile: ../docker/Dockerfile.frontend
    ports:
      - "3000:80"
    volumes:
      - ./frontend/public:/usr/share/nginx/html

  db:
    image: mariadb:latest
    environment:
      MYSQL_ROOT_PASSWORD: root_pass
      MYSQL_DATABASE: employee_db
      MYSQL_USER: employee_user
      MYSQL_PASSWORD: employee_pass
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

### 18.2 `Dockerfile.backend`

```dockerfile
FROM php:8.3-apache
RUN apt-get update && apt-get install -y libzip-dev zip unzip git \
    && docker-php-ext-install pdo_mysql zip intl
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
```

### 18.3 Migration & Seeder

```bash
# Jalankan di dalam container backend
php spark migrate
php spark db:seed InitialSeeder   # seed roles, permissions, superadmin user
```

### 18.4 Default Superadmin Credentials

```
Username : superadmin
Password : (auto-generate, tampil sekali saat seeder berjalan)
```

---

## 19. API Documentation

- Dokumentasi API menggunakan **Postman Collection**.
- File spec: `docs/api/postman_collection.json`.
- Impor file tersebut ke Postman untuk melihat daftar endpoint, parameter, dan contoh response.

---

## 20. Testing Requirements

### 20.1 Backend (PHPUnit)

Buat test untuk hal-hal berikut:

| Test Case                                     | Priority |
|-----------------------------------------------|----------|
| Login berhasil dengan kredensial valid        | High     |
| Login gagal dengan password salah            | High     |
| Login gagal jika akun nonaktif               | High     |
| Akses endpoint tanpa token → 401             | High     |
| Akses endpoint dengan role tidak sesuai → 403| High     |
| Kalkulasi tunjangan transport (berbagai skenario) | High |
| CRUD pegawai (happy path)                    | Medium   |
| Validasi input pegawai (NIP duplikat, dll)   | Medium   |
| Force logout setelah status diubah nonaktif  | Medium   |

### 20.2 Contoh Test Kalkulasi Tunjangan

```php
class TunjanganTransportTest extends TestCase {
  public function testKalkulasiNormal() {
    // base_fare=2500, jarak=12.5, hari_masuk=22
    // jarak_dibulatkan = 13 (0.5 → ceil)
    // total = 2500 * 13 * 22 = 715000
    $this->assertEquals(715000, hitungTunjangan(2500, 12.5, 22));
  }

  public function testBawaMinimalHari() {
    // hari_masuk=16 < 19 → tidak dapat tunjangan
    $this->assertEquals(0, hitungTunjangan(2500, 12.5, 16));
  }

  public function testJarakDiBawahMinimal() {
    // jarak=5km → tidak dapat tunjangan
    $this->assertEquals(0, hitungTunjangan(2500, 5, 22));
  }

  public function testJarakMelebihiMaksimal() {
    // jarak=30km → dihitung hanya 25km
    // total = 2500 * 25 * 22 = 1375000
    $this->assertEquals(1375000, hitungTunjangan(2500, 30, 22));
  }
}
```

### 20.3 Dokumentasi Testing

Buat file `docs/TESTING.md` yang berisi:
- Daftar test case beserta expected result
- Cara menjalankan test: `php spark test` atau `./vendor/bin/phpunit`
- Screenshot/output hasil test (jika memungkinkan)

---

## 21. Assumptions & Decisions

Berikut asumsi yang dibuat karena informasi tidak tersedia eksplisit di soal:

| # | Asumsi                                                                                          |
|---|-------------------------------------------------------------------------------------------------|
| 1 | "Pegawai Tetap" di dashboard = pegawai dengan jabatan Manager atau Staf (bukan Magang)         |
| 2 | "Pegawai Kontrak" di dashboard = pegawai dengan jabatan Staf                                   |
| 3 | Field `jenis_kelamin` ENUM('L','P') ditambahkan ke tabel `pegawai` (dibutuhkan dashboard chart)|
| 4 | Tunjangan transport hanya untuk jabatan Manager dan Staf (bukan Magang)                        |
| 5 | Foto pegawai disimpan di storage lokal (public/uploads), path relatif disimpan di DB           |
| 6 | Password user baru ditampilkan sekali di modal response setelah create                         |
| 7 | Data wilayah (kecamatan, kabupaten, provinsi) di-seed dari data wilayah Indonesia resmi         |
| 8 | Satu periode (bulan+tahun) hanya boleh ada 1 record tunjangan per pegawai (UNIQUE constraint)  |
| 9 | Tunjangan transport hanya untuk jabatan Staf dan Manager (bukan Magang)                        |
| 10| Captcha diimplementasikan sebagai image captcha sederhana menggunakan GD library PHP           |
| 11| Export PDF menggunakan library TCPDF atau mPDF via Composer                                    |
| 12| Export Excel menggunakan library PhpSpreadsheet via Composer                                   |
| 13| RO pada Kelola User untuk Manager HRD dan Admin HRD berarti mereka hanya bisa melihat dan mengupdate data diri sendiri |

---

*Dokumen ini bersifat agent-ready. Setiap section mengandung informasi yang cukup untuk langsung diimplementasikan. Gunakan file ini sebagai ground truth saat mengerjakan tiap modul.*
