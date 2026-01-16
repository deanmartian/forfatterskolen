<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerateRoyaltyVerificationReport extends Command
{
    protected $signature = 'royalty:verify-phase0 {--limit=5}';

    protected $description = 'Generate the Phase 0 royalty verification report.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $multiplier = config('royalties.storage_distribution_multiplier');

        $fkSql = <<<SQL
SELECT
    COUNT(*) AS total_rows,
    SUM(CASE WHEN pr.id IS NOT NULL THEN 1 ELSE 0 END) AS matches_project_registrations,
    SUM(CASE WHEN pb.id IS NOT NULL THEN 1 ELSE 0 END) AS matches_project_books
FROM storage_distribution_costs sdc
LEFT JOIN project_registrations pr ON pr.id = sdc.project_book_id
LEFT JOIN project_books pb ON pb.id = sdc.project_book_id;
SQL;

        $fkResult = DB::select($fkSql);

        $summarySql = <<<SQL
SELECT
    pr.id AS project_registration_id,
    COALESCE(sales.total_sales, 0) AS total_sales,
    COALESCE(dist.total_distributions, 0) * ? AS total_distributions,
    COALESCE(sales.total_sales, 0) - COALESCE(dist.total_distributions, 0) * ? AS total_payout,
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
ORDER BY ABS(COALESCE(sales.total_sales, 0) - COALESCE(dist.total_distributions, 0) * ?) DESC;
SQL;

        $summaryRows = collect(DB::select($summarySql, [$multiplier, $multiplier, $multiplier]));

        $selectedIds = $this->selectSampleRegistrations($summaryRows, $limit);

        if ($selectedIds === []) {
            $this->error('No eligible project_registrations found for report.');

            return Command::FAILURE;
        }

        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));

        $detailSql = <<<SQL
SELECT
    base.project_registration_id,
    base.year,
    base.quarter,
    COALESCE(sales.total_sales, 0) AS total_sales,
    COALESCE(dist.total_distributions, 0) * ? AS total_distributions,
    COALESCE(sales.total_sales, 0) - COALESCE(dist.total_distributions, 0) * ? AS net_payout,
    COALESCE(payouts.is_paid, 0) AS is_paid
FROM (
    SELECT
        pr.id AS project_registration_id,
        YEAR(pbs.date) AS year,
        QUARTER(pbs.date) AS quarter
    FROM project_registrations pr
    JOIN project_books pb ON pb.project_id = pr.project_id
    JOIN project_book_sales pbs ON pbs.project_book_id = pb.id
    WHERE pr.id IN ($placeholders)
    GROUP BY pr.id, year, quarter
    UNION
    SELECT
        project_book_id AS project_registration_id,
        YEAR(date) AS year,
        QUARTER(date) AS quarter
    FROM storage_distribution_costs
    WHERE project_book_id IN ($placeholders)
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
    WHERE pr.id IN ($placeholders)
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
    WHERE project_book_id IN ($placeholders)
    GROUP BY project_book_id, year, quarter
) dist ON dist.project_registration_id = base.project_registration_id
    AND dist.year = base.year
    AND dist.quarter = base.quarter
LEFT JOIN storage_payouts payouts ON payouts.project_registration_id = base.project_registration_id
    AND payouts.year = base.year
    AND payouts.quarter = base.quarter
ORDER BY base.project_registration_id, base.year DESC, base.quarter ASC;
SQL;

        $bindings = array_merge([$multiplier, $multiplier], $selectedIds, $selectedIds, $selectedIds, $selectedIds);

        $detailRows = collect(DB::select($detailSql, $bindings));

        $report = $this->buildReport(
            $multiplier,
            $fkSql,
            $fkResult,
            $summarySql,
            $summaryRows,
            $detailSql,
            $selectedIds,
            $detailRows,
        );

        $reportPath = base_path('reports/phase0-royalty-verification.md');
        $reportDir = dirname($reportPath);
        if (! File::isDirectory($reportDir)) {
            File::makeDirectory($reportDir, 0755, true);
        }
        File::put($reportPath, $report);

        $this->info("Report written to {$reportPath}");

        return Command::SUCCESS;
    }

    private function selectSampleRegistrations($summaryRows, int $limit): array
    {
        $selected = [];
        $addId = function (?int $id) use (&$selected): void {
            if ($id && ! in_array($id, $selected, true)) {
                $selected[] = $id;
            }
        };

        $positive = $summaryRows->firstWhere('total_payout', '>', 0);
        $negative = $summaryRows->firstWhere('total_payout', '<', 0);
        $paid = $summaryRows->firstWhere('paid_count', '>', 0);
        $unpaid = $summaryRows->firstWhere('unpaid_count', '>', 0);

        $addId($positive->project_registration_id ?? null);
        $addId($negative->project_registration_id ?? null);
        $addId($paid->project_registration_id ?? null);
        $addId($unpaid->project_registration_id ?? null);

        foreach ($summaryRows as $row) {
            $addId($row->project_registration_id);
            if (count($selected) >= $limit) {
                break;
            }
        }

        return array_slice($selected, 0, $limit);
    }

    private function buildReport(
        float $multiplier,
        string $fkSql,
        array $fkResult,
        string $summarySql,
        $summaryRows,
        string $detailSql,
        array $selectedIds,
        $detailRows
    ): string {
        $fkRow = $fkResult[0] ?? (object) ['total_rows' => 0, 'matches_project_registrations' => 0, 'matches_project_books' => 0];

        $summaryTable = $this->renderTable(
            ['project_registration_id', 'total_sales', 'total_distributions', 'total_payout', 'paid_count', 'unpaid_count'],
            $summaryRows->whereIn('project_registration_id', $selectedIds)->values()->all()
        );

        $detailsTable = $this->renderTable(
            ['project_registration_id', 'year', 'quarter', 'total_sales', 'total_distributions', 'net_payout', 'is_paid'],
            $detailRows->all()
        );

        $idList = implode(', ', $selectedIds);

        $sampleCount = count($selectedIds);

        return <<<MD
# Phase 0 Royalty Verification Report

## Multiplier
- storage/distribution multiplier (config `royalties.storage_distribution_multiplier`): **{$multiplier}**

## FK Verification (storage_distribution_costs.project_book_id)
**SQL**
```sql
{$fkSql}
```

**Result**
| total_rows | matches_project_registrations | matches_project_books |
| --- | --- | --- |
| {$fkRow->total_rows} | {$fkRow->matches_project_registrations} | {$fkRow->matches_project_books} |

## Sample project_registrations (limit {$sampleCount})
Selected project_registration IDs: **{$idList}**

**Selection summary SQL**
```sql
{$summarySql}
```

**Selection summary bindings**
- multiplier: {$multiplier}

**Selection summary results**
{$summaryTable}

## Per year/quarter verification
**SQL**
```sql
{$detailSql}
```

**Bindings**
- multiplier: {$multiplier}
- project_registration_ids: {$idList}

**Results**
{$detailsTable}

MD;
    }

    private function renderTable(array $headers, array $rows): string
    {
        $lines = [];
        $lines[] = '| '.implode(' | ', $headers).' |';
        $lines[] = '| '.implode(' | ', array_fill(0, count($headers), '---')).' |';

        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $header) {
                $line[] = data_get($row, $header);
            }
            $lines[] = '| '.implode(' | ', $line).' |';
        }

        return implode("\n", $lines);
    }
}
