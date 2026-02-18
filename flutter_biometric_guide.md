# Flutter Biometric (Fingerprint) Implementation Guide

This guide outlines the implementation of biometric authentication for the Beneficiary Mobile App using the existing Laravel Sanctum backend.

## 1. Overview
The implementation follows the **Secure Vault Pattern**. We utilize the device's hardware-encrypted storage to store user credentials, which are only released once a successful biometric verification is performed by the system.

## 2. Recommended Flutter Packages
- **Biometric Detection**: [`local_auth`](https://pub.dev/packages/local_auth)
- **Secure Encrypted Storage**: [`flutter_secure_storage`](https://pub.dev/packages/flutter_secure_storage)
- **Handling API Requests**: [`dio`](https://pub.dev/packages/dio)

---

## 3. Implementation Steps

### Phase A: Registration (First-time Setup)
1. **Manual Login**: User enters Email and Password in the app.
2. **Backend Authentication**: App calls `POST /api/v1/login`.
3. **Opt-in**: Upon successful login, display a dialog: *"Enable Fingerprint Login for next time?"*.
4. **Secure Save**: If accepted, use `flutter_secure_storage` to save the `email` and `password`.
   - *Note: These are encrypted at the OS level (Keychain for iOS / Keystore for Android) and cannot be accessed by other apps.*

### Phase B: Verification (Subsequent Logins)
1. **Trigger**: On the Login screen, show a Fingerprint icon or auto-trigger the biometric prompt.
2. **Local Auth**: Call `local_auth.authenticate()`.
3. **Credential Retrieval**: If success, retrieve the `email` and `password` from Secure Storage.
4. **Silent Login**: Post these credentials to the backend immediately.

---

## 4. API Reference

**Endpoint**: `POST /api/v1/login`  
**Headers**: 
- `Accept: application/json`
- `Content-Type: application/json`

**Payload**:
```json
{
  "email": "user@example.com",
  "password": "user_stored_password_from_secure_vault"
}
```

**Successful Response (200 OK)**:
The app will receive a standard Sanctum bearer token. 
```json
{
    "success": true,
    "data": {
        "user": { ... },
        "token": "1|AbCdEfG..." 
    },
    "message": "Login successful"
}
```

---

## 5. Security & Edge Cases
- **Revoking Access**: If the user clicks "Logout", the app should clear the `token` from memory but optionally ask: *"Would you like to keep Fingerprint login enabled?"*.
- **Hardware Changes**: Detection of new fingerprints added to the device should ideally trigger a re-authentication via password for safety (handled by `local_auth` configuration).
- **Password Rotations**: If the backend returns a `401 Unauthorized` (e.g., password changed elsewhere), the app MUST clear the secure storage and force a manual login.

---
**Prepared for**: Mobile Development Team  
**System**: Beneficiary App Backend (Laravel 10 / Sanctum)
