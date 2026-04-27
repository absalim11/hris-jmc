# Backend Milestones

- 2026-04-27T05:52:35+07:00: M16 - Rate limiting - Done
  - Added: `backend/app/Filters/RateLimitFilter.php` (file-based per-IP/per-user window limiter)
  - Updated: `backend/app/Config/Filters.php` (registered `ratelimit` filter applied before `api/*`)

- 2026-04-27T06:04:00+07:00: M17 - Activity Log API - Done
  - Added: `backend/app/Models/ActivityLogModel.php`
  - Added: `backend/app/Controllers/Api/ActivityLogController.php`
  - Updated: `backend/app/Config/Routes.php` (added `logs` routes)

- 2026-04-27T06:13:01+07:00: M18 - RBAC sync & verification - Done
  - Added: `GET /api/rbac/matrix` (backend/app/Controllers/Api/RbacController.php)
  - Backend exports role -> permissions matrix used by frontend to build route access map

- 2026-04-27T06:13:01+07:00: M19 - Dashboard per-role API support - Done
  - Added: `GET /api/dashboard` (backend/app/Controllers/Api/DashboardController.php index/admin/manager)
  - Admin endpoint returns aggregate tunjangan total and widgets; manager endpoint returns manager-specific widgets

- 2026-04-27T06:13:01+07:00: M20 - Smoke E2E tests & docs - Created
  - File: `frontend/M20_SMOKE_TESTS.md` created with detailed smoke-test checklist

