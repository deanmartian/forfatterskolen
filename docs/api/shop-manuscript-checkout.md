# Shop Manuscript Checkout API

Base path: `/api/v1`

Authentication: bearer token used for authenticated `/api/v1` routes.

## Plan lookup by word count

`GET /api/v1/shop-manuscripts/by-word-count?word_count=<int>`

Returns the first `shop_manuscripts` record where `max_words >= word_count`, ordered by `max_words` ascending.

Authentication: not required for this lookup endpoint.

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

- `genre` (required, integer id that exists in `genre` table)
- `payment_mode_id` (required, existing `payment_modes.id`; `Vipps` uses Vipps, and `Svea` or `Faktura`/id=3 are treated as Svea checkout)
- `payment_plan_id` (required, existing `payment_plans.id`)
- `email` (optional, used as fallback for provider checkout prefill)
- `zip` (optional, used as fallback for provider checkout prefill)
- `phone` (optional, used as fallback for provider checkout prefill)
- `manuscript` (required file: `docx,pdf,doc,odt`)
- `description` (optional)
- `synopsis` (optional file: `pdf,doc,docx,odt`)
- `coaching_time_later` (optional boolean)
- `send_to_email` (optional boolean)
- `word_count` (optional, recommended for doc/docx uploads)

### Example response

```json
{
  "redirect_url": "https://checkout.svea.com/...",
  "gui_snippet": "<div data-sco-sveacheckout-iframesrc=\"https://checkout.svea.com/...\"></div>",
  "reference": 101
}
```

---

## 2) Get checkout status

`GET /api/v1/learner/shop-manuscripts/checkout/{orderId}`

### Headers

- `Authorization: Bearer <token>`

For `Svea` orders, this endpoint also checks provider status (similar to `/api/v1/checkout/status/{reference}` patterns) and marks the order paid when Svea reports a finalized payment.

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
