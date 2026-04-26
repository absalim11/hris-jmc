# Employee Management System - Technical & UAT Guide

Dokumentasi ini berisi panduan untuk menjalankan aplikasi (Backend & Frontend) menggunakan Docker, serta panduan alur kerja untuk pengujian UAT.

---

## 1. Persyaratan Sistem (Technical Requirements)
Pastikan sistem Anda sudah terinstall:
- **Docker** & **Docker Compose**
- **Git**
- **Postman** (Opsional, untuk testing API langsung)

---

## 2. Cara Menjalankan Aplikasi (Setup Guide)

Ikuti langkah-langkah berikut untuk menjalankan seluruh service:

### Langkah 1: Build dan Jalankan Container
Buka terminal di root direktori proyek, lalu jalankan:
```bash
cd hris/docker
docker-compose up -d --build
```

### Langkah 2: Setup Database & Permissions
Berikan izin akses tulis untuk folder writable (khusus pengguna Linux):
```bash
chmod -R 777 hris/backend/writable hris/backend/public/uploads
```

Eksekusi migrasi dan seeder di dalam container backend:
```bash
docker-compose exec backend php spark migrate
docker-compose exec backend php spark db:seed InitialSeeder
```

### Langkah 3: Akses Aplikasi
- **Frontend (UI):** [http://localhost:3000/login.html](http://localhost:3000/login.html)
- **Backend (API):** [http://localhost:8080](http://localhost:8080)
- **Database Admin (Adminer):** [http://localhost:8081](http://localhost:8081)
  - **System:** `MySQL`
  - **Server:** `db_jmc`
  - **Username:** `employee_user`
  - **Password:** `employee_pass`
  - **Database:** `employee_db`

---

## 3. Data Uji Awal (Initial Credentials)

Gunakan akun berikut untuk masuk pertama kali:
- **Username:** `superadmin`
- **Password:** `Admin@123`

---

## 4. Skenario Pengujian UAT (UAT Test Cases)

Berikut adalah panduan alur untuk pengujian User Acceptance Test:

### A. Autentikasi & Keamanan
1. **Login:** Masukkan username dan password. Pastikan diarahkan ke Dashboard.
2. **RBAC (Akses Menu):**
   - Login sebagai **Superadmin**: Harus melihat menu 'Users'.
   - Login sebagai **Admin HRD**: Harus melihat menu 'Pegawai' dan 'Tunjangan'.
3. **Session Security:** Pastikan jika Anda menghapus token di browser atau logout, Anda tidak bisa mengakses halaman dashboard secara langsung.

### B. Pengelolaan Data Pegawai (Admin HRD)
1. **Tambah Pegawai:** Klik 'Tambah Pegawai', isi form lengkap (NIP minimal 8 angka).
2. **Filter & Search:** Gunakan kolom pencarian NIP atau Nama. Pastikan tabel ter-update secara otomatis.
3. **Export PDF:** Klik tombol Export PDF dan pastikan dokumen terunduh dengan data yang benar.

### C. Kalkulasi Tunjangan Transport (Admin HRD)
1. **Setting Tarif:** Masuk ke menu Setting Tunjangan, atur `base_fare` (contoh: 2500).
2. **Input Tunjangan:**
   - Masukkan ID Pegawai yang valid (Jabatan: Staf atau Manager).
   - Masukkan Jarak (contoh: 12.5 km) dan Hari Masuk (contoh: 22 hari).
   - **Verifikasi Real-time:** Pastikan sistem menampilkan preview Rp 715.000 (2500 * 13km * 22hari).
3. **Validasi Aturan Bisnis:**
   - Coba masukkan Hari Masuk = 15. Pastikan tunjangan menjadi Rp 0 (Syarat min. 19 hari).
   - Coba masukkan Jarak = 4 km. Pastikan tunjangan menjadi Rp 0 (Syarat min. 5 km).

### D. Audit Log (Superadmin)
1. Lakukan beberapa aksi (tambah pegawai/login/logout).
2. Login sebagai Superadmin.
3. Cek tabel `activity_log` di database atau (jika sudah ada UI Log) pastikan setiap aksi terekam dengan IP dan User Agent yang sesuai.

---

## 5. Troubleshooting
- **Database Connection Error:** Pastikan container `db` sudah dalam status *healthy* sebelum menjalankan migrasi.
- **Port Conflict:** Jika port 8080 atau 3000 sudah digunakan aplikasi lain, ubah mapping port di `hris/docker/docker-compose.yml`.
- **File Upload Error:** Pastikan folder `hris/backend/public/uploads/foto` memiliki izin tulis (writable).

---
*Dibuat oleh Senior Developer JMC Indonesia - 2026*
