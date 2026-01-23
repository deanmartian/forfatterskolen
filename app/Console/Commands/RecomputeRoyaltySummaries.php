<?php

namespace App\Console\Commands;

use App\RoyaltySummary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecomputeRoyaltySummaries extends Command
{
    protected $signature = 'royalty:recompute {--year=} {--quarter=} {--all}';

    protected $description = 'Recompute royalty summary rows for authors and registrations.';

    public function handle(): int
    {
        $yearOption = $this->option('year');
        $quarterOption = $this->option('quarter');
        $all = (bool) $this->option('all');

        if ($quarterOption !== null && (! is_numeric($quarterOption) || (int) $quarterOption < 1 || (int) $quarterOption > 4)) {
            $this->error('Quarter must be between 1 and 4.');

            return Command::FAILURE;
        }

        $years = $this->resolveYears($yearOption, $all);
        if (empty($years)) {
            $this->warn('No years found to recompute.');

            return Command::SUCCESS;
        }

        $quarters = $quarterOption ? [(int) $quarterOption] : [1, 2, 3, 4];

        foreach ($years as $year) {
            foreach ($quarters as $quarter) {
                $this->info("Recomputing summaries for {$year} Q{$quarter}...");
                $this->recomputePeriod((int) $year, (int) $quarter);
            }
        }

        $this->info('Royalty summaries recomputed.');

        return Command::SUCCESS;
    }

    private function resolveYears($yearOption, bool $all): array
    {
        if ($yearOption) {
            return [(int) $yearOption];
        }

        if (! $all) {
            return [now()->year];
        }

        $salesYears = DB::table('project_book_sales')
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->pluck('year')
            ->filter()
            ->all();

        $costYears = DB::table('storage_distribution_costs')
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->pluck('year')
            ->filter()
            ->all();

        $years = array_values(array_unique(array_merge($salesYears, $costYears)));
        sort($years);

        return $years;
    }

    private function recomputePeriod(int $year, int $quarter): void
    {
        $distributionMultiplier = config('royalties.storage_distribution_multiplier');
        $now = now();

        DB::transaction(function () use ($year, $quarter, $distributionMultiplier, $now) {
            RoyaltySummary::query()
                ->where('year', $year)
                ->where('quarter', $quarter)
                ->delete();

            $primaryRegistrations = DB::table('project_registrations as registrations')
                ->selectRaw('registrations.project_id, MIN(registrations.id) as primary_registration_id')
                ->join('projects', 'registrations.project_id', '=', 'projects.id')
                ->where('registrations.field', 'central-distribution')
                ->where('registrations.in_storage', 1)
                ->groupBy('registrations.project_id')
                ->pluck('primary_registration_id', 'project_id');

            $salesData = DB::table('project_books as books')
                ->selectRaw('books.project_id, SUM(sales.amount) as total_sales')
                ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
                ->whereYear('sales.date', $year)
                ->whereRaw('QUARTER(sales.date) = ?', [$quarter])
                ->groupBy('books.project_id')
                ->get()
                ->keyBy('project_id')
                ->map(function ($row) {
                    return (float) $row->total_sales;
                })
                ->toArray();

            $costsData = DB::table('storage_distribution_costs as costs')
                ->selectRaw('registrations.id as project_registration_id, SUM(costs.amount) as total_costs')
                ->join('project_registrations as registrations', 'costs.project_book_id', '=', 'registrations.id')
                // NOTE: storage_distribution_costs.project_book_id refers to project_registrations.id for royalty logic.
                ->whereYear('costs.date', $year)
                ->whereRaw('QUARTER(costs.date) = ?', [$quarter])
                ->where('registrations.field', 'central-distribution')
                ->where('registrations.in_storage', 1)
                ->groupBy('registrations.id')
                ->get()
                ->keyBy('project_registration_id')
                ->map(function ($row) {
                    return (float) $row->total_costs;
                })
                ->toArray();

            DB::table('project_registrations as registrations')
                ->select('registrations.id', 'registrations.id as project_registration_id', 'registrations.project_id', 'projects.user_id')
                ->join('projects', 'registrations.project_id', '=', 'projects.id')
                ->where('registrations.field', 'central-distribution')
                ->where('registrations.in_storage', 1)
                ->orderBy('registrations.id')
                ->chunkById(500, function ($chunk) use (
                    $year,
                    $quarter,
                    $primaryRegistrations,
                    $salesData,
                    $costsData,
                    $distributionMultiplier,
                    $now
                ) {
                    $rows = [];

                    foreach ($chunk as $registration) {
                        $salesAmount = 0.0;
                        if ((int) ($primaryRegistrations[$registration->project_id] ?? 0) === (int) $registration->project_registration_id) {
                            $salesAmount = $salesData[$registration->project_id] ?? 0.0;
                        }

                        $costBase = $costsData[$registration->project_registration_id] ?? 0.0;
                        $costMultiplied = $costBase * $distributionMultiplier;
                        $net = $salesAmount - $costMultiplied;

                        if ($salesAmount == 0.0 && $costBase == 0.0) {
                            continue;
                        }

                        $rows[] = [
                            'user_id' => $registration->user_id,
                            'year' => $year,
                            'quarter' => $quarter,
                            'project_registration_id' => $registration->project_registration_id,
                            'sales_amount' => round($salesAmount, 2),
                            'cost_amount_base' => round($costBase, 2),
                            'cost_amount_multiplied' => round($costMultiplied, 2),
                            'net_amount' => round($net, 2),
                            'computed_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (! empty($rows)) {
                        RoyaltySummary::insert($rows);
                    }
                });
        });
    }
}
