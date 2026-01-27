# Lovable API updates (next steps)

## What was done
- Hardened `/api/v1/courses/for-sale` with 10-minute caching and absolute `thumbnail_url` values, plus explicit absolute checkout URLs.
- Added `/api/v1/health` public endpoint returning `{ ok, env, time }`.
- Added portal endpoint for invoices (last 10).
- Ensured CORS is allowlist-driven for `/api/v1/*` and disallows wildcard origins in production.

## Example curl calls

```bash
curl -sS https://<your-domain>/api/v1/health
```

```bash
curl -sS https://<your-domain>/api/v1/courses/for-sale
```

```bash
curl -sS \
  -H "Authorization: Bearer <jwt>" \
  https://<your-domain>/api/v1/invoices
```

## Production verification checklist
- Confirm `LOVABLE_CORS_ORIGINS` is set to the Lovable domains (comma-separated) without `*`.
- Verify `GET /api/v1/courses/for-sale` returns absolute `thumbnail_url` and `checkout_url` values.
- Ensure Lovable frontend can authenticate without CORS issues.
- Validate portal endpoint returns data for a real user:
  - `GET /api/v1/invoices` returns the most recent 10 invoices.
- Check logs/queue to confirm no new failed jobs were created.
