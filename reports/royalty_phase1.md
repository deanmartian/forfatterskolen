# Phase 1 Author-First Royalty Admin UI Notes

## Calculation mapping to Phase 0 verification
- Sales totals follow the same grouping as the Storage Details and `Frontend\LearnerController@bookSale` logic: sales are summed from `project_book_sales.amount` by `project_books.project_id` for the selected year/quarter (or all quarters when none is selected).
- Storage/distribution costs follow the same table used in Phase 0 verification queries and Storage Details: `storage_distribution_costs.amount` is summed by registration/quarter and then multiplied by the configured royalty multiplier (1.2).
- **Important mapping:** `storage_distribution_costs.project_book_id` is treated as the `project_registration_id` for royalty calculations, matching the existing storage cost export logic.

## Paid status (Phase 1)
- **Paid** means *all relevant project registrations for the author and period* have a `storage_payouts` record marked `is_paid = 1`.
- When a quarter is selected, only that quarter is checked.
- When no quarter is selected, only quarters with sales or costs are checked; any missing or unpaid payout marks the registration (and author) as unpaid.

## Assumptions and edge cases
- Registrations are limited to `project_registrations` with `field = central-distribution` and `in_storage = 1`, mirroring the storage details view.
- Projects without sales or costs in the period remain visible so that the **No Sales** status can be applied.
- If an author has activity but no corresponding payout record for the selected period, the author is shown as **Payable** (unless the net payout is negative).
