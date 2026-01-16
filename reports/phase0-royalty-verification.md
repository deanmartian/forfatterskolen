# Phase 0 Royalty Verification Report

> Note: The local environment does not have a running MySQL server or vendor dependencies available, so live query results could not be fetched here. The SQL below is ready to run against the production or staging database to return the 5 required project_registrations (positive, negative, paid, unpaid). This report uses the configured multiplier from `config/royalties.php` (default **1.2**).

## Multiplier (single source of truth)
```sql
SET @storage_distribution_multiplier := 1.2;
```

## FK verification: storage_distribution_costs.project_book_id
Use this to confirm whether `storage_distribution_costs.project_book_id` stores `project_registrations.id` (overloaded FK) vs `project_books.id`.

```sql
SELECT
    COUNT(*) AS total_rows,
    SUM(CASE WHEN pr.id IS NOT NULL THEN 1 ELSE 0 END) AS matches_project_registrations,
    SUM(CASE WHEN pb.id IS NOT NULL THEN 1 ELSE 0 END) AS matches_project_books
FROM storage_distribution_costs sdc
LEFT JOIN project_registrations pr ON pr.id = sdc.project_book_id
LEFT JOIN project_books pb ON pb.id = sdc.project_book_id;
```

**Result (run in DB):**
| total_rows | matches_project_registrations | matches_project_books |
| --- | --- | --- |
| _N/A (DB not available in local environment)_ | _N/A_ | _N/A_ |

## Sample selection query (5 project_registrations)
This query identifies candidate project_registrations with sales and/or storage/distribution costs, sorted by payout magnitude.

```sql
SELECT
    pr.id AS project_registration_id,
    COALESCE(sales.total_sales, 0) AS total_sales,
    COALESCE(dist.total_distributions, 0) * @storage_distribution_multiplier AS total_distributions,
    COALESCE(sales.total_sales, 0) - COALESCE(dist.total_distributions, 0) * @storage_distribution_multiplier AS total_payout,
    COALESCE(payouts.paid_count, 0) AS paid_count,
    COALESCE(payouts.unpaid_count, 0) AS unpaid_count
FROM project_registrations pr
LEFT JOIN (
    SELECT pb.project_id, SUM(pbs.amount) AS total_sales
    FROM project_books pb
    LEFT JOIN project_book_sales pbs ON pbs.project_book_id = pb.id
    GROUP BY pb.project_id
) sales ON sales.project_id = pr.project_id
LEFT JOIN (
    SELECT project_book_id AS project_registration_id, SUM(amount) AS total_distributions
    FROM storage_distribution_costs
    GROUP BY project_book_id
) dist ON dist.project_registration_id = pr.id
LEFT JOIN (
    SELECT
        project_registration_id,
        SUM(CASE WHEN is_paid = 1 THEN 1 ELSE 0 END) AS paid_count,
        SUM(CASE WHEN is_paid = 0 THEN 1 ELSE 0 END) AS unpaid_count
    FROM storage_payouts
    GROUP BY project_registration_id
) payouts ON payouts.project_registration_id = pr.id
WHERE pr.in_storage = 1
  AND (sales.total_sales IS NOT NULL OR dist.total_distributions IS NOT NULL)
ORDER BY ABS(COALESCE(sales.total_sales, 0) - COALESCE(dist.total_distributions, 0) * @storage_distribution_multiplier) DESC
LIMIT 25;
```

**Result (use this to pick 5 IDs covering positive, negative, paid, unpaid):**
| project_registration_id | total_sales | total_distributions | total_payout | paid_count | unpaid_count |
| --- | --- | --- | --- | --- | --- |
| _N/A (DB not available in local environment)_ | | | | | |

## Per-year / per-quarter verification for selected project_registrations
Replace the `IN (...)` list with the 5 selected IDs from the query above.

```sql
SELECT
    base.project_registration_id,
    base.year,
    base.quarter,
    COALESCE(sales.total_sales, 0) AS total_sales,
    COALESCE(dist.total_distributions, 0) * @storage_distribution_multiplier AS total_distributions,
    COALESCE(sales.total_sales, 0) - COALESCE(dist.total_distributions, 0) * @storage_distribution_multiplier AS net_payout,
    COALESCE(payouts.is_paid, 0) AS is_paid
FROM (
    SELECT
        pr.id AS project_registration_id,
        YEAR(pbs.date) AS year,
        QUARTER(pbs.date) AS quarter
    FROM project_registrations pr
    JOIN project_books pb ON pb.project_id = pr.project_id
    JOIN project_book_sales pbs ON pbs.project_book_id = pb.id
    WHERE pr.id IN (/* five ids here */)
    GROUP BY pr.id, year, quarter
    UNION
    SELECT
        project_book_id AS project_registration_id,
        YEAR(date) AS year,
        QUARTER(date) AS quarter
    FROM storage_distribution_costs
    WHERE project_book_id IN (/* five ids here */)
    GROUP BY project_book_id, year, quarter
) base
LEFT JOIN (
    SELECT
        pr.id AS project_registration_id,
        YEAR(pbs.date) AS year,
        QUARTER(pbs.date) AS quarter,
        SUM(pbs.amount) AS total_sales
    FROM project_registrations pr
    JOIN project_books pb ON pb.project_id = pr.project_id
    JOIN project_book_sales pbs ON pbs.project_book_id = pb.id
    WHERE pr.id IN (/* five ids here */)
    GROUP BY pr.id, year, quarter
) sales ON sales.project_registration_id = base.project_registration_id
    AND sales.year = base.year
    AND sales.quarter = base.quarter
LEFT JOIN (
    SELECT
        project_book_id AS project_registration_id,
        YEAR(date) AS year,
        QUARTER(date) AS quarter,
        SUM(amount) AS total_distributions
    FROM storage_distribution_costs
    WHERE project_book_id IN (/* five ids here */)
    GROUP BY project_book_id, year, quarter
) dist ON dist.project_registration_id = base.project_registration_id
    AND dist.year = base.year
    AND dist.quarter = base.quarter
LEFT JOIN storage_payouts payouts ON payouts.project_registration_id = base.project_registration_id
    AND payouts.year = base.year
    AND payouts.quarter = base.quarter
ORDER BY base.project_registration_id, base.year DESC, base.quarter ASC;
```

**Result (run in DB):**
| project_registration_id | year | quarter | total_sales | total_distributions | net_payout | is_paid |
| --- | --- | --- | --- | --- | --- | --- |
| _N/A (DB not available in local environment)_ | | | | | | |
