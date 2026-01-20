<?php

namespace App\Services;

use App\AuthorPayout;
use App\AuthorPayoutItem;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoyaltyService
{
    public function getAuthorSummary(int $year, ?int $quarter, ?string $status, ?string $search): Collection
    {
        $data = $this->buildRoyaltyData($year, $quarter, $search, null);

        $authors = collect();
        $quarters = $this->quartersForPeriod($quarter);

        $salesByProjectId = [];

        foreach ($data['registrations'] as $registration) {
            $salesTotals = $this->sumSalesForProject($data['salesByProjectQuarter'], $registration->project_id, $quarters);
            if (($salesByProjectId[$registration->project_id] ?? false) === true) {
                $salesTotals = 0.0;
            }
            $costsTotals = $this->sumCostsForRegistration($data['costsByRegistrationQuarter'], $registration->project_registration_id, $quarters);
            $net = $salesTotals - $costsTotals;

            $quartersWithActivity = $this->quartersWithActivity(
                $data['salesByProjectQuarter'],
                $data['costsByRegistrationQuarter'],
                $registration->project_id,
                $registration->project_registration_id,
                $quarters
            );
            $isPaid = $this->registrationPaidStatus(
                $data['paidByRegistrationQuarter'],
                $registration->project_registration_id,
                $quartersWithActivity
            );

            if (! $authors->has($registration->user_id)) {
                $authors->put($registration->user_id, [
                    'user_id' => $registration->user_id,
                    'name' => trim("{$registration->first_name} {$registration->last_name}"),
                    'email' => $registration->email,
                    'total_sales' => 0.0,
                    'total_costs' => 0.0,
                    'net_payout' => 0.0,
                    'registrations' => [],
                    'paid' => false,
                    'activity_quarters' => [],
                ]);
            }

            $author = $authors->get($registration->user_id);
            $author['total_sales'] += $salesTotals;
            $author['total_costs'] += $costsTotals;
            $author['net_payout'] += $net;
            $author['registrations'][] = [
                'project_registration_id' => $registration->project_registration_id,
                'project_id' => $registration->project_id,
                'project_name' => $registration->project_name,
                'book_name' => $registration->book_name,
                'sales' => $salesTotals,
                'costs' => $costsTotals,
                'net_payout' => $net,
                'quarters_with_activity' => $quartersWithActivity,
                'paid' => $isPaid,
            ];
            $salesByProjectId[$registration->project_id] = true;

            $author['activity_quarters'] = array_values(array_unique(array_merge(
                $author['activity_quarters'],
                $quartersWithActivity
            )));

            $authors->put($registration->user_id, $author);
        }

        $authors = $authors->values()->map(function (array $author) use ($data, $quarter) {
            $author['paid'] = $this->authorPaidStatus(
                $data['paidByAuthorQuarter'],
                $author['user_id'],
                $quarter,
                $author['activity_quarters']
            );
            $author['status'] = $this->authorStatus($author['total_sales'], $author['total_costs'], $author['net_payout'], $author['paid']);

            return $author;
        });

        if ($status) {
            $authors = $authors->filter(fn (array $author) => $author['status'] === $status)->values();
        }

        return $authors->sortByDesc('net_payout')->values();
    }

    public function getAuthorDetails(int $userId, int $year, ?int $quarter): array
    {
        $user = User::findOrFail($userId);
        $data = $this->buildRoyaltyData($year, $quarter, null, $userId);
        $quarters = $this->quartersForPeriod($quarter);

        $salesByProjectId = [];

        $registrations = $data['registrations']->map(function ($registration) use ($data, $quarters, &$salesByProjectId) {
            $salesTotals = $this->sumSalesForProject($data['salesByProjectQuarter'], $registration->project_id, $quarters);
            if (($salesByProjectId[$registration->project_id] ?? false) === true) {
                $salesTotals = 0.0;
            }
            $costsTotals = $this->sumCostsForRegistration($data['costsByRegistrationQuarter'], $registration->project_registration_id, $quarters);
            $net = $salesTotals - $costsTotals;
            $quartersWithActivity = $this->quartersWithActivity(
                $data['salesByProjectQuarter'],
                $data['costsByRegistrationQuarter'],
                $registration->project_id,
                $registration->project_registration_id,
                $quarters
            );

            $salesByProjectId[$registration->project_id] = true;

            return [
                'project_registration_id' => $registration->project_registration_id,
                'project_id' => $registration->project_id,
                'project_name' => $registration->project_name,
                'book_name' => $registration->book_name,
                'sales' => $salesTotals,
                'costs' => $costsTotals,
                'net_payout' => $net,
                'paid' => $this->registrationPaidStatus(
                    $data['paidByRegistrationQuarter'],
                    $registration->project_registration_id,
                    $quartersWithActivity
                ),
            ];
        })->sortByDesc(function (array $registration) {
            return abs($registration['net_payout']);
        })->values();

        return [
            'user' => $user,
            'registrations' => $registrations,
            'totals' => [
                'sales' => $registrations->sum('sales'),
                'costs' => $registrations->sum('costs'),
                'net_payout' => $registrations->sum('net_payout'),
            ],
        ];
    }

    public function computeAuthorPayout(int $userId, int $year, int $quarter): array
    {
        $data = $this->buildRoyaltyData($year, $quarter, null, $userId);
        $quarters = $this->quartersForPeriod($quarter);

        $items = [];
        $total = 0.0;

        $salesByProjectId = [];

        foreach ($data['registrations'] as $registration) {
            $salesTotals = $this->sumSalesForProject($data['salesByProjectQuarter'], $registration->project_id, $quarters);
            if (($salesByProjectId[$registration->project_id] ?? false) === true) {
                $salesTotals = 0.0;
            }
            $costsTotals = $this->sumCostsForRegistration($data['costsByRegistrationQuarter'], $registration->project_registration_id, $quarters);

            if ($salesTotals == 0.0 && $costsTotals == 0.0) {
                continue;
            }

            $net = $salesTotals - $costsTotals;

            $items[] = [
                'project_registration_id' => $registration->project_registration_id,
                'project_id' => $registration->project_id,
                'project_name' => $registration->project_name,
                'book_name' => $registration->book_name,
                'sales' => $salesTotals,
                'costs' => $costsTotals,
                'net_payout' => $net,
            ];

            $total += $net;
            $salesByProjectId[$registration->project_id] = true;
        }

        return [
            'total' => $total,
            'items' => $items,
        ];
    }

    public function createOrUpdateAuthorPayout(
        int $userId,
        int $year,
        int $quarter,
        ?string $note,
        int $paidByUserId
    ): array {
        $handler = function () use ($userId, $year, $quarter, $note, $paidByUserId) {
            $payout = AuthorPayout::where('user_id', $userId)
                ->where('year', $year)
                ->where('quarter', $quarter)
                ->lockForUpdate()
                ->first();

            if ($payout && $payout->paid_at) {
                return [
                    'status' => 'already_paid',
                    'payout' => $payout,
                    'total' => (float) $payout->amount_total,
                ];
            }

            $computed = $this->computeAuthorPayout($userId, $year, $quarter);
            $amountTotal = round($computed['total'], 2);

            if ($amountTotal <= 0.0) {
                return [
                    'status' => 'not_payable',
                    'payout' => $payout,
                    'total' => $amountTotal,
                ];
            }

            if (! $payout) {
                $payout = AuthorPayout::create([
                    'user_id' => $userId,
                    'year' => $year,
                    'quarter' => $quarter,
                    'amount_total' => $amountTotal,
                    'paid_at' => now(),
                    'paid_by_user_id' => $paidByUserId,
                    'note' => $note,
                ]);
            } else {
                $payout->fill([
                    'amount_total' => $amountTotal,
                    'paid_at' => now(),
                    'paid_by_user_id' => $paidByUserId,
                    'note' => $note,
                ]);
                $payout->save();
                $payout->items()->delete();
            }

            $items = collect($computed['items'])->map(function (array $item) use ($payout) {
                return [
                    'author_payout_id' => $payout->id,
                    'project_registration_id' => $item['project_registration_id'],
                    'amount' => round($item['net_payout'], 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            if ($items->isNotEmpty()) {
                AuthorPayoutItem::insert($items->all());
            }

            return [
                'status' => $payout->wasRecentlyCreated ? 'created' : 'updated',
                'payout' => $payout,
                'total' => $amountTotal,
            ];
        };

        try {
            return DB::transaction($handler);
        } catch (QueryException $exception) {
            if ($this->isDuplicatePayoutException($exception)) {
                return DB::transaction($handler);
            }

            throw $exception;
        }
    }

    private function buildRoyaltyData(int $year, ?int $quarter, ?string $search, ?int $userId): array
    {
        $distributionMultiplier = config('royalties.storage_distribution_multiplier');

        $registrationQuery = DB::table('project_registrations as registrations')
            ->select(
                'registrations.id as project_registration_id',
                'registrations.project_id',
                'projects.user_id',
                'projects.name as project_name',
                'book_names.book_name',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->join('projects', 'registrations.project_id', '=', 'projects.id')
            ->join('users', 'projects.user_id', '=', 'users.id')
            ->leftJoinSub(
                DB::table('project_books')
                    ->selectRaw('project_id, MIN(book_name) as book_name')
                    ->groupBy('project_id'),
                'book_names',
                'book_names.project_id',
                '=',
                'projects.id'
            )
            ->where('registrations.field', 'central-distribution')
            ->where('registrations.in_storage', 1);

        if ($userId) {
            $registrationQuery->where('projects.user_id', $userId);
        }

        if ($search) {
            $registrationQuery->where(function ($query) use ($search) {
                $query->where('users.first_name', 'like', "%{$search}%")
                    ->orWhere('users.last_name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('projects.name', 'like', "%{$search}%")
                    ->orWhere('book_names.book_name', 'like', "%{$search}%");
            });
        }

        $registrations = $registrationQuery->get();

        $salesData = DB::table('project_books as books')
            ->selectRaw('books.project_id, QUARTER(sales.date) as quarter, SUM(sales.amount) as total_sales')
            ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
            ->whereYear('sales.date', $year)
            ->when($quarter, function ($query) use ($quarter) {
                $query->whereRaw('QUARTER(sales.date) = ?', [$quarter]);
            })
            ->groupBy('books.project_id', 'quarter')
            ->get();

        $costsData = DB::table('storage_distribution_costs as costs')
            ->selectRaw('registrations.id as project_registration_id, registrations.project_id, QUARTER(costs.date) as quarter, SUM(costs.amount) as total_costs')
            ->join('project_registrations as registrations', 'costs.project_book_id', '=', 'registrations.id')
            // NOTE: storage_distribution_costs.project_book_id refers to project_registrations.id for royalty logic.
            ->whereYear('costs.date', $year)
            ->where('registrations.field', 'central-distribution')
            ->where('registrations.in_storage', 1)
            ->when($quarter, function ($query) use ($quarter) {
                $query->whereRaw('QUARTER(costs.date) = ?', [$quarter]);
            })
            ->groupBy('registrations.id', 'registrations.project_id', 'quarter')
            ->get()
            ->map(function ($row) use ($distributionMultiplier) {
                $row->total_costs = $row->total_costs * $distributionMultiplier;

                return $row;
            });

        $payoutsData = DB::table('storage_payouts')
            ->select('project_registration_id', 'quarter', 'is_paid')
            ->where('year', $year)
            ->when($quarter, function ($query) use ($quarter) {
                $query->where('quarter', $quarter);
            })
            ->get();

        $authorPayoutsData = DB::table('author_payouts')
            ->select('user_id', 'quarter', 'paid_at')
            ->where('year', $year)
            ->when($quarter, function ($query) use ($quarter) {
                $query->where('quarter', $quarter);
            })
            ->get();

        return [
            'registrations' => $registrations,
            'salesByProjectQuarter' => $this->groupSales($salesData),
            'costsByRegistrationQuarter' => $this->groupCosts($costsData),
            'paidByRegistrationQuarter' => $this->groupPayouts($payoutsData),
            'paidByAuthorQuarter' => $this->groupAuthorPayouts($authorPayoutsData),
        ];
    }

    private function groupSales(Collection $salesData): array
    {
        return $salesData->groupBy('project_id')->map(function ($rows) {
            return $rows->keyBy('quarter')->map(function ($row) {
                return (float) $row->total_sales;
            })->toArray();
        })->toArray();
    }

    private function groupCosts(Collection $costsData): array
    {
        return $costsData->groupBy('project_registration_id')->map(function ($rows) {
            return $rows->keyBy('quarter')->map(function ($row) {
                return (float) $row->total_costs;
            })->toArray();
        })->toArray();
    }

    private function groupPayouts(Collection $payoutsData): array
    {
        return $payoutsData->groupBy('project_registration_id')->map(function ($rows) {
            return $rows->keyBy('quarter')->map(function ($row) {
                return (bool) $row->is_paid;
            })->toArray();
        })->toArray();
    }

    private function groupAuthorPayouts(Collection $payoutsData): array
    {
        return $payoutsData->groupBy('user_id')->map(function ($rows) {
            return $rows->keyBy('quarter')->map(function ($row) {
                return $row->paid_at !== null;
            })->toArray();
        })->toArray();
    }

    private function quartersForPeriod(?int $quarter): array
    {
        return $quarter ? [$quarter] : [1, 2, 3, 4];
    }

    private function sumSalesForProject(array $salesByProjectQuarter, int $projectId, array $quarters): float
    {
        $total = 0.0;
        foreach ($quarters as $quarter) {
            $total += $salesByProjectQuarter[$projectId][$quarter] ?? 0.0;
        }

        return $total;
    }

    private function sumCostsForRegistration(array $costsByRegistrationQuarter, int $registrationId, array $quarters): float
    {
        $total = 0.0;
        foreach ($quarters as $quarter) {
            $total += $costsByRegistrationQuarter[$registrationId][$quarter] ?? 0.0;
        }

        return $total;
    }

    private function quartersWithActivity(
        array $salesByProjectQuarter,
        array $costsByRegistrationQuarter,
        int $projectId,
        int $registrationId,
        array $quarters
    ): array {
        $quartersWithActivity = [];

        foreach ($quarters as $quarter) {
            $sales = $salesByProjectQuarter[$projectId][$quarter] ?? 0.0;
            $costs = $costsByRegistrationQuarter[$registrationId][$quarter] ?? 0.0;

            if ($sales != 0.0 || $costs != 0.0) {
                $quartersWithActivity[] = $quarter;
            }
        }

        return $quartersWithActivity;
    }

    private function registrationPaidStatus(array $paidByRegistrationQuarter, int $registrationId, array $quartersWithActivity): bool
    {
        if (empty($quartersWithActivity)) {
            return false;
        }

        foreach ($quartersWithActivity as $quarter) {
            if (! ($paidByRegistrationQuarter[$registrationId][$quarter] ?? false)) {
                return false;
            }
        }

        return true;
    }

    private function authorPaidStatus(array $paidByAuthorQuarter, int $userId, ?int $quarter, array $activityQuarters): bool
    {
        if ($quarter) {
            return $paidByAuthorQuarter[$userId][$quarter] ?? false;
        }

        if (empty($activityQuarters)) {
            return false;
        }

        foreach ($activityQuarters as $activityQuarter) {
            if (! ($paidByAuthorQuarter[$userId][$activityQuarter] ?? false)) {
                return false;
            }
        }

        return true;
    }

    private function authorStatus(float $sales, float $costs, float $net, bool $paid): string
    {
        if ($sales == 0.0 && $costs == 0.0) {
            return 'no-sales';
        }

        if ($net < 0.0) {
            return 'negative';
        }

        if ($paid) {
            return 'paid';
        }

        return 'payable';
    }

    private function isDuplicatePayoutException(QueryException $exception): bool
    {
        return (string) $exception->getCode() === '23000'
            || str_contains($exception->getMessage(), 'Duplicate entry');
    }
}
