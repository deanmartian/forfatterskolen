<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\RoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoyaltyController extends Controller
{
    public function index(Request $request, RoyaltyService $royaltyService): View
    {
        $currentYear = now()->year;

        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:'.($currentYear + 1),
            'quarter' => 'nullable|integer|min:1|max:4',
            'status' => 'nullable|in:payable,paid,negative,no-sales',
            'search' => 'nullable|string',
        ]);

        $year = (int) ($validated['year'] ?? $currentYear);
        $quarter = isset($validated['quarter']) ? (int) $validated['quarter'] : null;
        $status = $validated['status'] ?? null;
        $search = $validated['search'] ?? null;

        $authors = $royaltyService->getAuthorSummary($year, $quarter, $status, $search);

        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $paginatedAuthors = new LengthAwarePaginator(
            $authors->forPage($page, $perPage)->values(),
            $authors->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $years = range(2024, $currentYear);
        $quarters = [1, 2, 3, 4];

        return view('backend.royalty.authors.index', [
            'authors' => $paginatedAuthors,
            'year' => $year,
            'quarter' => $quarter,
            'status' => $status,
            'search' => $search,
            'years' => $years,
            'quarters' => $quarters,
        ]);
    }

    public function show(Request $request, int $userId, RoyaltyService $royaltyService): View
    {
        $currentYear = now()->year;

        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:'.($currentYear + 1),
            'quarter' => 'nullable|integer|min:1|max:4',
        ]);

        $year = (int) ($validated['year'] ?? $currentYear);
        $quarter = isset($validated['quarter']) ? (int) $validated['quarter'] : null;

        $details = $royaltyService->getAuthorDetails($userId, $year, $quarter);
        $years = range(2024, $currentYear);
        $quarters = [1, 2, 3, 4];

        return view('backend.royalty.authors.show', [
            'author' => $details['user'],
            'registrations' => $details['registrations'],
            'totals' => $details['totals'],
            'year' => $year,
            'quarter' => $quarter,
            'years' => $years,
            'quarters' => $quarters,
        ]);
    }

    public function markPaid(Request $request, RoyaltyService $royaltyService): RedirectResponse
    {
        $currentYear = now()->year;

        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:'.($currentYear + 1),
            'quarter' => 'required|integer|min:1|max:4',
            'author_ids' => 'required|array',
            'author_ids.*' => 'integer',
            'note' => 'nullable|string|max:500',
        ]);

        $year = (int) $validated['year'];
        $quarter = (int) $validated['quarter'];
        $note = $validated['note'] ?? null;
        $paidByUserId = (int) $request->user()->id;

        $created = 0;
        $updated = 0;
        $alreadyPaid = 0;
        $total = 0.0;

        foreach ($validated['author_ids'] as $authorId) {
            $result = $royaltyService->createOrUpdateAuthorPayout(
                (int) $authorId,
                $year,
                $quarter,
                $note,
                $paidByUserId
            );

            $total += $result['total'];

            if ($result['status'] === 'created') {
                $created++;
            } elseif ($result['status'] === 'updated') {
                $updated++;
            } else {
                $alreadyPaid++;
            }
        }

        $message = 'Author payouts processed. '
            .'Created: '.$created.'. Updated: '.$updated.'. Already paid: '.$alreadyPaid.'. '
            .'Total payout: '.number_format($total, 2);

        session()->flash('message.content', $message);

        return redirect()->route('admin.royalty.authors.index', [
            'year' => $year,
            'quarter' => $quarter,
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ]);
    }
}
