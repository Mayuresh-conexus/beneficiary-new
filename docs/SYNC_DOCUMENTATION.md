# Sync Mechanism Documentation

## Overview
Field Connect uses a **client-side draft queue** with automatic sync when online connectivity is restored. This ensures volunteers can collect beneficiary data in low-connectivity field environments.

## Architecture

```
┌──────────────────────┐          ┌──────────────────────┐
│   Mobile Client      │          │   Laravel Backend     │
│                      │          │                       │
│  ┌────────────────┐  │          │  ┌─────────────────┐  │
│  │ Form Input     │──┼──online──┼──│ POST /api/v1/   │  │
│  │                │  │          │  │ beneficiaries   │  │
│  └────────────────┘  │          │  └─────────────────┘  │
│          │           │          │                       │
│       offline        │          │                       │
│          ▼           │          │                       │
│  ┌────────────────┐  │          │                       │
│  │ localStorage   │  │          │                       │
│  │ (fc_drafts)    │──┼──sync────┼──▶ Same endpoint     │
│  └────────────────┘  │          │                       │
└──────────────────────┘          └──────────────────────┘
```

## How It Works

### 1. Online Submission (Normal Flow)
- Form data ➜ `POST /api/v1/beneficiaries` ➜ Success response
- Documents ➜ `POST /api/v1/upload` (per document) ➜ Success

### 2. Offline Submission (Draft Queue)
When network is unavailable:
1. Form data saved to `localStorage` under key `fc_drafts`
2. Each draft gets a unique `draft_id` (timestamp) and `draft_time`
3. User redirected to Drafts screen with confirmation toast

### 3. Auto-Sync on Reconnection
```javascript
window.addEventListener('online', () => {
    // Automatically iterates through drafts
    // Submits each via API
    // Removes from localStorage on success
    // Shows toast per synced item
    syncDrafts();
});
```

### 4. Manual Sync
User can tap "Sync Now" button on Drafts screen to trigger `syncDrafts()`.

## Data Storage

| Key | Type | Contents |
|-----|------|----------|
| `fc_token` | string | Sanctum bearer token |
| `fc_user` | JSON | User object (id, name, email, role) |
| `fc_drafts` | JSON array | Pending beneficiary submissions |

## Conflict Resolution
- **Server is source of truth** — drafts are one-way push
- **No server-side drafts** — pending count on server always = 0
- **Duplicate prevention** — `government_id` unique constraint prevents duplicate submissions
- **Failed syncs** — draft remains in queue for retry

## Sync Status Endpoint
`GET /api/v1/sync-status` returns:
```json
{
  "last_sync": "2024-01-01T10:00:00Z",
  "server_time": "2024-01-01T12:00:00Z",
  "pending_submissions": 0,
  "total_submitted": 42
}
```

## Document Upload Sync
Documents are uploaded **after** the beneficiary record is created:
1. Submit beneficiary ➜ get `beneficiary_id`
2. For each document: `POST /api/v1/upload` with `beneficiary_id`
3. **Note**: If offline, documents are stored as File objects in memory (not in localStorage due to size). They sync when the draft syncs, but only if the page hasn't been refreshed. For production, consider IndexedDB for file blob storage.
