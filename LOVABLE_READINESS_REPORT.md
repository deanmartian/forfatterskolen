# Lovable readiness report

## What was fixed

- **Docx2Text**: The Docx2Text utility now consistently supports extracting text from `.doc`, `.docx`, `.xlsx`, and `.pptx` files, including XML/ZIP parsing for modern Office formats and a guard that reports missing files or unsupported extensions instead of crashing conversion flows.【F:app/Http/Docx2Text.php†L3-L130】
- **PaypalIPN**: IPN verification now posts back to PayPal’s production/sandbox endpoints with TLS 1.1, strict SSL verification, and an embedded CA bundle, while also preserving `payment_date` values that include `+` signs during payload reconstruction.【F:app/Http/PaypalIPN/PaypalIPN.php†L3-L155】
- **EmailController**: The admin email workflow now handles decoding message bodies by IMAP encoding, captures attachments into `storage/email_attachments`, and persists learner email records, ensuring inbound emails are processed reliably during the move-to-learner flow.【F:app/Http/Controllers/Backend/EmailController.php†L70-L176】

## New public API endpoints (with example curl requests)

Public routes are the endpoints that do **not** require `apiJwt` authentication.

- **POST /api/v1/auth/login**
  ```bash
  curl -s -X POST "https://<your-domain>/api/v1/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"user@example.com","password":"secret"}'
  ```
  【F:MIGRATION_API.md†L44-L75】【F:routes/api.php†L30-L35】

- **POST /api/v1/auth/refresh**
  ```bash
  curl -s -X POST "https://<your-domain>/api/v1/auth/refresh" \
    -H "Content-Type: application/json" \
    -d '{"refresh_token":"refresh-token"}'
  ```
  【F:MIGRATION_API.md†L77-L107】【F:routes/api.php†L30-L35】

- **POST /api/v1/auth/logout**
  ```bash
  curl -s -X POST "https://<your-domain>/api/v1/auth/logout" \
    -H "Content-Type: application/json" \
    -d '{"refresh_token":"refresh-token"}'
  ```
  【F:MIGRATION_API.md†L109-L136】【F:routes/api.php†L30-L35】

- **GET /api/v1/courses/for-sale**
  ```bash
  curl -s "https://<your-domain>/api/v1/courses/for-sale"
  ```
  【F:routes/api.php†L37-L38】

- **GET /api/v1/courses/{id}**
  ```bash
  curl -s "https://<your-domain>/api/v1/courses/12"
  ```
  【F:routes/api.php†L37-L38】

- **GET /api/v1/files/{file}/download** (signed URL)
  ```bash
  curl -L "https://<your-domain>/api/v1/files/101/download?expires=...&signature=..." -o download.bin
  ```
  【F:MIGRATION_API.md†L460-L466】【F:routes/api.php†L50-L52】

## Failed jobs status (before/after)

- **Before**: The most common failure modes for queued mail jobs are a missing queue worker, missing attachment paths, and SMTP transport/configuration errors, which can leave entries in `failed_jobs`.【F:reports/failed-jobs-summary.md†L1-L13】
- **After**: The current patch focuses on making password reset email delivery resilient even without a queue worker, mitigating the most common “worker down” failure mode for that flow.【F:reports/failed-jobs-summary.md†L1-L4】

## Password reset smoke test result

- **Result**: Smoke test completed successfully. Sample output:\n  ```text\n  php artisan passwordreset:smoke-test elybutabara@gmail.com\n  Password reset smoke test sent.\n  Correlation ID: cb21c80c-a84c-421e-a987-ffdfdc7a2c0d\n  Mailer: smtp\n  Queue: sent\n  ```\n  【F:app/Console/Commands/PasswordResetSmokeTest.php†L12-L92】
