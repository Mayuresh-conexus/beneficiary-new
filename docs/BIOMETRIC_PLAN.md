# Biometric Verification Plan (Fingerprint)

## 1. Architecture Overview

To verify that aid is provided to the correct beneficiary, we will implement a **1:1 Verification** system.
*   **Enrollment**: Capture fingerprint template during registration.
*   **Verification**: Capture fingerprint again at distribution to match against the stored record.

**Recommendation**: Use the **Flutter App** with an **External USB Scanner** (e.g., Mantra MFS100). The PWA has severe limitations accessing USB hardware on iOS/Android.

## 2. Database Changes
We need to store the **Biometric Template** (a digital code representing the fingerprint features), not just the image.

**New Columns in `beneficiaries` table:**
- `biometric_template` (TEXT/LONGTEXT): Stores the ISO/FMR minutiae string.
- `biometric_enralled_at` (TIMESTAMP): When it was captured.
- `biometric_type` (STRING): e.g., 'FINGERPRINT_ISO', 'FACE_EMBEDDING'.

## 3. Workflow

### Phase A: Enrollment (Registration)
1.  **UI Update**: Add "Capture Fingerprint" button to **Step 2 (Documents)** or a new **Step 3**.
2.  **Action**: Volunteer connects USB scanner.
3.  **Logic**: App reads raw data from scanner SDK -> Converts to ISO Template (Base64 string).
4.  **Submission**: Send this string in the `create_beneficiary` API payload.

### Phase B: Verification (Distribution)
1.  **UI**: In `BeneficiaryDetailScreen`, add a "Verify Identity" button.
2.  **Action**:
    - Volunteer connects scanner.
    - App fetches the stored `biometric_template` from the server for this beneficiary.
    - Volunteer scans the beneficiary's finger live.
3.  **Matching**:
    - The App SDK compares **Live Scan** vs **Stored Template**.
    - **Match Found**: Show "✅ Identity Confirmed". Proceed to log distribution.
    - **No Match**: Show "❌ Mismatch". Warn the volunteer.

## 4. Technical Stack

### Flutter (Mobile App)
*   **Package**: Use a plugin specific to the hardware (e.g., `mantra_mfs100` package or platform channel).
*   **Logic**:
    *   Enrollment: `Scanner.capture() -> returning ISO_String`.
    *   Verification: `Scanner.match(live_iso, stored_iso) -> returning Score`.

### Laravel (Backend)
*   **API Update**:
    *   Update `BeneficiaryController@store` to accept `biometric_template`.
    *   No complex matching logic needed on server if we do **Match-on-Device** (simpler/faster).
    *   Optional: Implement server-side matching (requires Java/Python microservice) for higher security.

## 5. PWA Strategy (The Challenge)
Browsers cannot easily talk to USB scanners on mobile.
*   **Workaround**: Use the **Camera** to capture a photo of the finger (Touchless Fingerprint).
*   **Constraint**: Accuracy is lower than USB scanners.
*   **Recommendation**: **Restrict Biometric features to the Flutter App** for reliability.

## 6. Next Steps
1.  **Confirm Hardware**: Will you procure USB scanners (approx \$30-50 each)?
2.  **Database Migration**: Add the columns.
3.  **Flutter Integration**: Integrate the SDK.
