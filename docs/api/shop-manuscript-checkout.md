# Shop Manuscript Checkout API

Base path: `/api/v1`

Authentication: same bearer token used for other authenticated `/api/v1` endpoints.

## Status values

- `pending`: checkout created, awaiting payment confirmation
- `paid`: payment confirmed and access granted
- `failed`: payment failed
- `cancelled`: checkout cancelled

## Idempotency

`POST /learner/shop-manuscripts/{id}/checkout` requires the `Idempotency-Key` request header.

For the same user + shop manuscript + idempotency key, the API returns the existing checkout order instead of creating duplicates.

---

## 1) Create checkout

`POST /api/v1/learner/shop-manuscripts/{id}/checkout`

### Headers

- `Authorization: Bearer <token>`
- `Idempotency-Key: <client-generated-unique-key>`

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

### Example response

```json
{
  "order_id": 101,
  "status": "paid",
  "amount": 1490,
  "currency": "NOK",
  "payment_provider": "vipps",
  "payment_url": "https://api.vipps.no/...",
  "message": "Payment captured and access granted."
}
```

---

## 3) Cancel checkout

`POST /api/v1/learner/shop-manuscripts/checkout/{orderId}/cancel`

### Headers

- `Authorization: Bearer <token>`

Cancels only pending orders. If provider remote cancellation is unavailable, order is still cancelled locally.

---

## Vipps webhook (if enabled)

`POST /api/v1/payments/vipps/shop-manuscripts/webhook`

Optional header verification:

- `X-Shopmanuscript-Webhook-Token: <token>` when `services.vipps.webhook_token` is configured.

This endpoint updates checkout state based on payment status and grants learner access on successful payment.
