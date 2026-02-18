# Beneficiary Package Delivery & Biometric Confirmation Guide

This guide explains how to handle the delivery of packages to beneficiaries, using fingerprint as a "note of confirmation."

## 1. Process Overview
1. **Search**: Volunteer searches for an approved beneficiary.
2. **Pending Items**: The app fetches all "Pending" transactions for that beneficiary.
3. **Selection**: Volunteer selects the items currently being handed over.
4. **Verification**: Beneficiary places their finger on the scanner. 
5. **Confirmation**: If it matches the stored template (fetched previously), the app marks the delivery as "Biometrically Verified."

---

## 2. API Reference for Delivery

**Endpoint**: `POST /api/v1/transactions/{transaction_id}/deliver`  
**Headers**: 
- `Authorization: Bearer [TOKEN]`
- `Accept: application/json`

**Payload**:
```json
{
  "biometric_verified": true,
  "biometric_device": "SecuGen Hamster Pro 20",
  "notes": "Handed over extra winter blanket as requested.",
  "signature_image": [Optional File Upload]
}
```

### Response (200 OK)
```json
{
    "success": true,
    "message": "Delivery recorded successfully with biometric confirmation",
    "data": {
        "status": "delivered",
        "biometric_verified": true,
        "biometric_verified_at": "2026-02-17T11:45:00Z"
    }
}
```

---

## 3. Manager Review (Selective Package Approval)
When a manager reviews a beneficiary, they can now approve or reject specific packages.

**Endpoint**: `POST /api/v1/beneficiary/{id}/review`  
**Payload**:
```json
{
  "action": "approve",
  "approved_package_ids": [1, 2, 5],
  "remarks": "Approving food and hygiene kits only."
}
```
- If `approved_package_ids` is provided, **ONLY** those IDs will generate pending delivery tasks.
- If it is empty, all currently assigned packages will be approved.

---

## 4. UI Best Practices for Developer
- **Verification Badge**: Show a green "Biometrically Verified" badge in the transaction history for confirmed deliveries.
- **Offline Delivery**: For locations without signal, store the verification timestamp locally and sync the `POST /deliver` requests when back online.

---
**System**: Beneficiary App Backend (Transaction Module)
