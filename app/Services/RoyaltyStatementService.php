<?php

namespace App\Services;

use App\AuthorPayout;
use App\RoyaltySummary;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RoyaltyStatementService
{
    public function generateForPayout(AuthorPayout $payout, bool $force = false): array
    {
        if ($payout->statement_path && ! $force) {
            return ['status' => 'skipped', 'path' => $payout->statement_path];
        }

        $statementData = $this->buildStatementData($payout->user_id, $payout->year, $payout->quarter, $payout);

        $pdf = Pdf::loadView('backend.royalty.statements.statement', $statementData);
        $path = $this->statementPath($payout, 'pdf');

        Storage::disk('local')->put($path, $pdf->output());

        $payout->statement_path = $path;
        $payout->save();

        return ['status' => 'generated', 'path' => $path];
    }

    public function generateBatch(int $year, int $quarter, bool $force = false): array
    {
        $summaryTotals = RoyaltySummary::query()
            ->select('user_id', DB::raw('SUM(net_amount) as net_total'))
            ->where('year', $year)
            ->where('quarter', $quarter)
            ->groupBy('user_id')
            ->having('net_total', '>', 0)
            ->get();

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($summaryTotals as $summary) {
            try {
                $payout = $this->resolvePayout((int) $summary->user_id, $year, $quarter);
                $result = $this->generateForPayout($payout, $force);

                if ($result['status'] === 'generated') {
                    $generated++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $exception) {
                $errors++;
                Log::error('Failed to generate royalty statement', [
                    'user_id' => $summary->user_id,
                    'year' => $year,
                    'quarter' => $quarter,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return [
            'generated' => $generated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    public function downloadPath(AuthorPayout $payout): ?string
    {
        return $payout->statement_path;
    }

    public function buildStatementData(int $userId, int $year, int $quarter, AuthorPayout $payout): array
    {
        $user = User::findOrFail($userId);
        $summaries = RoyaltySummary::query()
            ->where('user_id', $userId)
            ->where('year', $year)
            ->where('quarter', $quarter)
            ->get()
            ->groupBy('project_registration_id');

        $registrations = DB::table('project_registrations as registrations')
            ->select(
                'registrations.id as project_registration_id',
                'registrations.project_id',
                'projects.name as project_name',
                'book_names.book_name'
            )
            ->join('projects', 'registrations.project_id', '=', 'projects.id')
            ->leftJoinSub(
                DB::table('project_books')
                    ->selectRaw('project_id, MIN(book_name) as book_name')
                    ->groupBy('project_id'),
                'book_names',
                'book_names.project_id',
                '=',
                'projects.id'
            )
            ->where('projects.user_id', $userId)
            ->where('registrations.field', 'central-distribution')
            ->where('registrations.in_storage', 1)
            ->get()
            ->map(function ($registration) use ($summaries) {
                $summary = $summaries->get($registration->project_registration_id, collect());
                $sales = (float) $summary->sum('sales_amount');
                $costBase = (float) $summary->sum('cost_amount_base');
                $costMultiplied = (float) $summary->sum('cost_amount_multiplied');
                $net = (float) $summary->sum('net_amount');

                if ($sales == 0.0 && $costBase == 0.0) {
                    return null;
                }

                return [
                    'project_registration_id' => $registration->project_registration_id,
                    'project_name' => $registration->project_name,
                    'book_name' => $registration->book_name,
                    'sales' => $sales,
                    'cost_base' => $costBase,
                    'cost_multiplied' => $costMultiplied,
                    'net' => $net,
                ];
            })
            ->filter()
            ->values();

        $totals = [
            'sales' => $registrations->sum('sales'),
            'cost_base' => $registrations->sum('cost_base'),
            'cost_multiplied' => $registrations->sum('cost_multiplied'),
            'net' => $registrations->sum('net'),
        ];

        return [
            'author' => $user,
            'year' => $year,
            'quarter' => $quarter,
            'registrations' => $registrations,
            'totals' => $totals,
            'payout' => $payout,
        ];
    }

    private function statementPath(AuthorPayout $payout, string $extension): string
    {
        return sprintf(
            'royalty-statements/author-%d/royalty-statement-%d-Q%d.%s',
            $payout->user_id,
            $payout->year,
            $payout->quarter,
            $extension
        );
    }

    private function resolvePayout(int $userId, int $year, int $quarter): AuthorPayout
    {
        $payout = AuthorPayout::where('user_id', $userId)
            ->where('year', $year)
            ->where('quarter', $quarter)
            ->first();

        if ($payout) {
            return $payout;
        }

        $royaltyService = app(RoyaltyService::class);
        $computed = $royaltyService->computeAuthorPayout($userId, $year, $quarter);

        return AuthorPayout::create([
            'user_id' => $userId,
            'year' => $year,
            'quarter' => $quarter,
            'amount_total' => round($computed['total'], 2),
            'paid_at' => null,
            'paid_by_user_id' => null,
            'note' => null,
        ]);
    }
}
