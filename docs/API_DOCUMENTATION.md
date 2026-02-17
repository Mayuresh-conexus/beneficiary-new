# Field Connect — API Documentation v1

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
All endpoints (except `POST /login`) require a Bearer token in the `Authorization` header:
```
Authorization: Bearer {token}
```

---

## Endpoints

### 🔐 Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/login` | Authenticate volunteer, returns token |
| `POST` | `/logout` | Revoke current token |
| `GET`  | `/me` | Get authenticated user profile |

#### POST /login
```json
// Request
{ "email": "volunteer@ngo.org", "password": "password" }

// Response 200
{
  "success": true,
  "data": {
    "user": { "id": 1, "name": "John Doe", "email": "...", "role": "volunteer", "organization_id": 1, "is_active": true },
    "token": "1|abc123..."
  },
  "message": "Login successful"
}
```

---

### 👤 Volunteer

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET`  | `/volunteer/dashboard` | Dashboard stats (submissions, approved, pending, etc.) |
| `GET`  | `/volunteer/projects` | List assigned projects with packages and stats |
| `GET`  | `/project/{id}/packages` | Get packages for a specific project |
| `GET`  | `/sync-status` | Check last sync time and server status |

---

### 📋 Beneficiaries

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/beneficiaries` | Submit a new beneficiary |
| `GET`  | `/beneficiaries/my-submissions` | List volunteer's own submissions |
| `GET`  | `/beneficiary/{id}` | Get beneficiary details with timeline |
| `PUT`  | `/beneficiary/{id}` | Resubmit a rejected beneficiary |
| `POST` | `/beneficiary/{id}/review` | Manager: Approve/Reject/Flag |

#### Query Parameters for `/my-submissions`:
- `status`: Filter by `submitted`, `approved`, `rejected`, `fraud`
- `search`: Search by name or government ID
- `per_page`: Results per page (default 15)

#### POST /beneficiaries
```json
{
  "first_name": "Sarah",
  "last_name": "Al-Fayed",
  "government_id": "899320-1123",
  "assigned_project_id": 1,
  "latitude": -1.3145,
  "longitude": 36.7845,
  "contact_number": "+254712345678",
  "address": "Kibera Zone 4, Nairobi",
  "date_of_birth": "1985-03-14",
  "gender": "female",
  "package_ids": [1, 2]
}
```

#### POST /beneficiary/{id}/review (Manager only)
```json
{ "action": "approve|reject|fraud", "remarks": "Required for reject/fraud" }
```

---

### 📤 Upload

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/upload` | Upload document (multipart/form-data) |

Fields: `beneficiary_id`, `type` (id_proof|income_proof|photo|consent_form|other), `file` (max 5MB, jpeg/jpg/png/pdf)

---

### 🔔 Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET`  | `/notifications` | List user notifications |
| `GET`  | `/notifications/unread-count` | Get unread count |
| `POST` | `/notifications/{id}/read` | Mark single as read |
| `POST` | `/notifications/mark-all-read` | Mark all as read |

---

## Error Responses
```json
{ "success": false, "message": "Error description" }
```

| Status | Meaning |
|--------|---------|
| 401 | Unauthorized / Token expired |
| 403 | Forbidden (role restriction) |
| 404 | Resource not found |
| 422 | Validation error |
| 500 | Server error |

---

## Roles & Permissions
| Role | Can Submit | Can Review | Can View All |
|------|-----------|------------|-------------|
| `volunteer` | ✅ | ❌ | Own only |
| `manager` | ✅ | ✅ | Org scope |
| `organization_admin` | ✅ | ✅ | Org scope |
| `super_admin` | ✅ | ✅ | All |
