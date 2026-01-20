# Phase 2 Author Payout Ledger Notes

## Paid status changes
- The author “Paid” badge on **Royalty → Authors** now comes from `author_payouts` for the selected year/quarter.
- An author is considered **Paid** when there is an `author_payouts` record for the user/year/quarter **with `paid_at` set**.
- When viewing **All quarters**, the author is only marked paid if each quarter that has royalty activity has a paid author payout entry.

## Totals verification (Phase 1 parity)
The payout ledger totals are computed using the same royalty logic as Phase 1:
- Sales totals are aggregated by project book sales per quarter.
- Distribution costs use the same multiplier configuration (`royalties.storage_distribution_multiplier`).
- `storage_distribution_costs.project_book_id` is treated as `project_registration_id` for cost attribution.

## Test plan
1. **Create payout**
   - Navigate to `/admin/royalty/authors`, select a specific year + quarter, choose an author, and click **Mark as paid**.
   - Confirm a new `author_payouts` row exists and the `author_payout_items` rows match the author’s royalty details for that quarter.
2. **Prevent duplicates**
   - Repeat **Mark as paid** for the same author/year/quarter.
   - Verify the UI reports **Already paid** and no duplicate payout rows are created.
3. **Totals match Phase 1**
   - Compare the stored `author_payouts.amount_total` with the **Net Payout** shown in the Phase 1 author details view for the same year/quarter.
   - Confirm each `author_payout_items.amount` equals the per-registration net shown in the details list.
