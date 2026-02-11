# Shop Manuscript Checkout API

Base path: `/api/v1`

Authentication: bearer token used for authenticated `/api/v1` routes.

## Status values

- `pending`: checkout created, awaiting payment confirmation
- `paid`: payment confirmed and access granted
- `failed`: payment failed
- `cancelled`: checkout cancelled

## Idempotency

`POST /learner/shop-manuscripts/{id}/checkout` requires `Idempotency-Key`.

The API reuses the same order for the same `user + manuscript + idempotency key`.

---

## 1) Create checkout

`POST /api/v1/learner/shop-manuscripts/{id}/checkout`

### Headers

- `Authorization: Bearer <token>`
- `Idempotency-Key: <client-generated-unique-key>`

### Multipart form-data body

- `genre` (required)
- `manuscript` (required file: `docx,pdf,doc,odt`)
- `description` (optional)
- `synopsis` (optional file: `pdf,doc,docx,odt`)
- `coaching_time_later` (optional boolean)
- `send_to_email` (optional boolean)
- `word_count` (optional, recommended for doc/docx uploads)

### Example response

```json
{
  "order_id": 101,
  "status": "pending",
  "amount": 1490,
  "currency": "NOK",
  "payment_provider": "manual",
  "payment_url": null,
  "message": "Order created. Payment is pending manual processing. Please contact support to complete payment."
}
```

---

## 2) Get checkout status

`GET /api/v1/learner/shop-manuscripts/checkout/{orderId}`

### Headers

- `Authorization: Bearer <token>`

---

## 3) Cancel checkout

`POST /api/v1/learner/shop-manuscripts/checkout/{orderId}/cancel`

### Headers

- `Authorization: Bearer <token>`

Cancels only pending manuscript checkout orders owned by the authenticated learner.

---

## Vipps webhook (if enabled)

`POST /api/v1/payments/vipps/shop-manuscripts/webhook`

Optional verification header:

- `X-Shopmanuscript-Webhook-Token: <token>` when `services.vipps.webhook_token` is set.

On successful provider status (`CAPTURED`/`RESERVED`), order is marked paid and manuscript access is provisioned.
