# Implementation Plan: Beneficiary App Full System

## STEP 1 — SYSTEM VALIDATION (API Audit)

### Required APIs vs Current State

| Required API | Current Status | Action |
|---|---|---|
| `POST /api/v1/login` | ❌ Exists at `/api/login` (no v1) | Restructure to v1 |
| `POST /api/v1/logout` | ❌ Exists at `/api/logout` (no v1) | Restructure to v1 |
| `GET /api/v1/me` | ❌ Exists at `/api/user` (no v1) | Restructure to v1 |
| `GET /api/v1/volunteer/projects` | ❌ Exists at `/api/projects` (no v1, no volunteer scope) | **CREATE** |
| `GET /api/v1/project/{id}/packages` | ❌ Exists at `/api/packages?project_id=` (query vs path) | **CREATE** |
| `POST /api/v1/beneficiaries` | ❌ Exists at `/api/beneficiaries` (no v1) | Restructure to v1 |
| `GET /api/v1/beneficiaries/my-submissions` | ❌ Missing | **CREATE** |
| `GET /api/v1/beneficiary/{id}` | ❌ Exists at `/api/beneficiaries/{id}` (no v1) | Restructure to v1 |
| `PUT /api/v1/beneficiary/{id}` | ❌ Missing | **CREATE** |
| `POST /api/v1/upload` | ❌ Exists at `/api/beneficiaries/{id}/upload` | Restructure to v1 |
| `GET /api/v1/sync-status` | ❌ Missing | **CREATE** |

### Findings
- No versioned API structure (v1)
- Missing endpoints: my-submissions, resubmit (PUT), sync-status
- Auth uses Sanctum ✅
- No Form Request validation classes
- No API Resource classes
- Activity logging exists ✅
- Notification system exists ✅

## STEP 2 — API FIXES NEEDED

1. Create versioned API routes under `/api/v1/`
2. Create `VolunteerController` for volunteer-specific endpoints
3. Create Form Requests for validation
4. Create API Resource classes
5. Add missing endpoints (my-submissions, resubmit, sync-status)
6. Keep backward compatibility for existing `/api/` routes

## STEP 3 — FLUTTER APP

Build as a web-based SPA (since Flutter SDK isn't available on this Windows machine).
Instead, build the **mobile web app** using the Stitch UI patterns as HTML/JS/CSS.

### Screens to Build (from Stitch UI):
1. Splash Screen
2. Volunteer Login
3. Volunteer Dashboard
4. Project Details
5. Add Beneficiary - Personal Info (Step 1)
6. Add Beneficiary - Documents (Step 3)
7. Review and Submit (Step 5)
8. Submission History
9. Status Detail & Rejection
10. Offline Mode & Drafts
