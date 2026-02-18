# How to Test Biometric Verification on Windows (Simulation)

Since you don't have a physical fingerprint scanner connected yet, we need to "fake" the enrollment data in the database to test the verification flow.

## Prerequisite: Ensure you have `biometric_template` data
The verification logic strictly checks if the beneficiary has a stored biometric template.

### Option 1: Use Laravel Tinker (Fastest)
1. Open your terminal in the project directory.
2. Run `php artisan tinker`.
3. Run the following commands to add a dummy template to a beneficiary (replace `1` with your target Beneficiary ID):

```php
$b = App\Models\Beneficiary::find(1);
$b->biometric_template = "DEMO_FINGERPRINT_TEMPLATE_STRING_BASE64";
$b->save();
exit;
```

### Option 2: Test the "Failure" Case
1. Find a beneficiary that you created *without* the mobile app (or just newly created).
2. Ensure they do NOT have a template (this is the default).
3. Try to verify them. You should see an error: "No biometric template found".

---

## Testing Steps

1. **Login** to the Web Portal as an Admin/Manager.
2. **Go to Beneficiaries** list.
3. Select a beneficiary that is **Approved**.
   - *Note*: If the beneficiary is "Submitted", approve them first using the "Approve" button.
4. Scroll down to the **"Package Approval"** panel (only visible for Approved beneficiaries).
5. Click the **"[DEMO] Simulate Scan"** button.
   - **Scenario A (Has Template)**: The spinner will spin for 1 second, then show a green "Identity Verified!" message. The "Approve & Generate Transactions" button will become clickable.
   - **Scenario B (No Template)**: You will get an alert saying "Verification Failed: No biometric template found".

## What's Happening Under the Hood?
1. The **Frontend** sends an AJAX request to `/api/v1/beneficiaries/{id}/verify-biometric`.
2. The **Backend** receives the request.
3. It checks the database: `Does this beneficiary have a value in the 'biometric_template' column?`
   - **Yes**: Success (Simulating a match).
   - **No**: Failure.

This confirms that your **Full Stack Integration** (Frontend Button -> Route -> Controller -> Database Check) is working correctly.
