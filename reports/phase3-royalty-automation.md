# Phase 3 Royalty Automation Notes

## Recompute royalty summaries

Recompute for a specific quarter:

```bash
php artisan royalty:recompute --year=2024 --quarter=2
```

Recompute for the current year (all quarters):

```bash
php artisan royalty:recompute
```

Recompute for every year found in sales/cost data:

```bash
php artisan royalty:recompute --all
```

A nightly scheduler entry is available in `App\Console\Kernel` (`02:00`).

## Generate statements

Generate statements for all payable authors in a quarter (admin UI):

1. Visit **Admin → Royalty → Authors**.
2. Select statement year/quarter.
3. Click **Generate statements for payable authors**.

Generate a single statement (admin UI):

1. Visit an author's royalty detail page.
2. In **Payout statements**, click **Generate**.

Statements are stored under `storage/app/royalty-statements/` and the PDF path is saved in `author_payouts.statement_path`.

## Verify totals (5 samples)

1. Pick 5 authors with activity in the same year/quarter.
2. For each author, compare totals on:
   - **Admin → Royalty → Authors** (list totals).
   - **Admin → Royalty → Author details** (sum of registrations).
3. Run the Phase 0 verification report for a quick spot-check:

```bash
php artisan royalty:verify-phase0 --limit=5
```

4. Ensure totals match between the Phase 0 report, the author details totals, and the author list totals.

## Edge cases / notes

- Sales are attributed once per project (legacy behavior) so only one registration per project holds sales for a quarter.
- Paid status is driven by `author_payouts.paid_at` (Phase 2) rather than legacy `storage_payouts`.
- Authors with registrations but no sales/costs still appear in the list as **No Sales** (totals = 0).
- If summaries are missing for a period, the system falls back to the legacy computation to avoid blocking the UI.
