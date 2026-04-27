# Comprehensive Implementation Plan: Laravel 12 Frontend Migration

This plan incorporates all requirements from the PRD and the Technical Test specifications (RBAC, Module Logic, and UI Rules).

## Phase 1: Foundation & Security (RBAC Core)
- [ ] **1.1 ApiService & Session Management:**
    - Refine `app/Services/ApiService.php` to handle Bearer Token injection.
    - Implement Session storage for: `access_token`, `user_info`, and `permissions` array.
- [ ] **1.2 Permission-Aware Middleware:**
    - Enhance `app/Http/Middleware/ApiAuthMiddleware.php`:
        - Redirect to `/login` if no session.
        - **Force Logout Check:** Query backend/database for `force_logout_queue` status on every request.
- [ ] **1.3 Master Layout & RBAC Sidebar:**
    - Create `resources/views/layouts/app.blade.php` with Basecoat.
    - **Dynamic Menu:** Hide/show menu items based on the RBAC Matrix (Superadmin vs Manager vs Admin).
    - Implement CSRF protection for all AJAX/Fetch calls.

## Phase 2: Auth Module (PRD #9 / Soal #1)
- [ ] **2.1 Multi-Identifier Login:**
    - `AuthController@login`: Support `username`, `email`, or `no_hp` as identifiers.
    - Implement **Captcha** validation logic (matching backend-generated code).
    - **Remember Me:** Set persistent cookie/session TTL (30 days vs 1 hour).
- [ ] **2.2 Views & Validation:**
    - `login.blade.php`: Styled with Basecoat.
    - **Onkeyup Validation:** Real-time feedback for password strength and identifier format.

## Phase 3: Employee (Pegawai) Management (PRD #13 / Soal #5)
- [ ] **3.1 List View (Data Table):**
    - Columns: No. Urut, NIP, Nama, Jabatan, Tanggal Masuk, Masa Kerja (auto-calc).
    - Features: Sorting (NIP, Nama, Jabatan, Tgl Masuk, Masa Kerja), Pagination, Bulk Select.
    - Buttons: Data Baru, Download PDF/Excel, Bulk Delete, Bulk Status Update.
    - **Filter:** Jabatan (multi-select), Masa Kerja (Operator: `>`, `=`, `<` + Year input).
- [ ] **3.2 Advanced Form (Create/Edit):**
    - **Rules:** NIP (min 8, digits only), Nama (Alpha + `'`), HP (International `+62`), Foto (PNG/JPG < 2MB).
    - **Auto-Calculations (Vanilla JS):** 
        - Auto-calculate `Usia` from `Tanggal Lahir` (field disabled).
        - Auto-calculate `Masa Kerja` on detail view.
    - **Vue Islands:**
        - `Kecamatan Autocomplete` (min 3 chars) -> Auto-fill Kab/Prov (disabled).
        - `Tempat Lahir Autocomplete` (min 3 chars).
        - `Dynamic Education List` (Add/Remove rows).
- [ ] **3.3 Permissions Enforcement:**
    - Manager HRD: Read Only (All).
    - Admin HRD: CRUD (Cannot delete Superadmin's employee data).

## Phase 4: Tunjangan Transport (PRD #14, #15 / Soal #6, #7)
- [ ] **4.1 Setting Tunjangan (Admin HRD Only):**
    - CRUD for `Base Fare` with "Berlaku Dari" history logic.
- [ ] **4.2 Tunjangan Input & Calculation:**
    - Formula: `base_fare * km_rounded * days`.
    - **Rounding Logic:** `< 0.5 floor`, `>= 0.5 ceil`.
    - **Eligibility Logic (Vue Island):** 
        - Min 19 days.
        - Max distance 25km (cap).
        - Min distance > 5km.
        - Only for "Pegawai Tetap" (Jabatan: Manager/Staf).
- [ ] **4.3 View Logic:**
    - Manager/Admin HRD: Read Only (Own records).

## Phase 5: User Management & Role (PRD #10, #11 / Soal #2, #3)
- [ ] **5.1 User CRUD (Superadmin Only for Create/Status):**
    - **Autosuggest Nama Pengguna:** Min 2 characters to suggest from `pegawai` table.
    - **Username Rules:** Min 6, lowercase, alphanumeric, unique.
    - **Password Rules:** Auto-generate on Create; Manual change on Profile. (8 chars, 1 Upper, 1 Lower, 1 Special).
    - **Force Logout Implementation:** If status toggled to Non-active, add to `force_logout_queue`.
- [ ] **5.2 Role & Permission Matrix:**
    - Superadmin tool to assign Modul+Aksi permissions.

## Phase 6: Dashboard (PRD #12 / Soal #4)
- [ ] **6.1 Role-Based Views:**
    - Superadmin/Admin: Welcome Message only.
    - **Manager HRD Full Dashboard:**
        - Widgets: Total Pegawai, Kontrak, Tetap, Magang.
        - Charts (Vue + Chart.js): Doughnut (Type Distribution), Doughnut (Gender).
        - Table: 5 newest "Pegawai Kontrak".

## Phase 7: Activity Logs (PRD #16 / Soal #8)
- [ ] **7.1 Audit Trail:**
    - View logs for: Login, Logout, Create, Read, Update, Delete.
    - Filter by Modul, Aksi, and Date Range.

## Phase 8: Final Validation
- [ ] **8.1 Global Validations:**
    - Ensure all `onkeyup` validations are consistent.
- [ ] **8.2 Smoke Test:**
    - Superadmin: Manage Roles/Users.
    - Admin HRD: CRUD Pegawai, Setup Tunjangan.
    - Manager HRD: View Dashboard, View Own Tunjangan.

---
*Status: Ready for Phase 1 Execution.*
