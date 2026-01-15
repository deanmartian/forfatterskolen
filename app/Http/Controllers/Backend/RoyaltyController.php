<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectBook;
use App\ProjectBookSale;
use App\ProjectRegistration;
use App\RoyaltyPayout;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoyaltyController extends Controller
{
    public function index(Request $request)
    {
        [$year, $quarter, $periodStart, $periodEnd] = $this->resolvePeriod($request);

        $authors = User::select('users.id', 'users.first_name', 'users.last_name')
            ->join('projects', 'projects.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderBy('users.first_name')
            ->orderBy('users.last_name')
            ->get();

        $bookCounts = ProjectBook::query()
            ->join('projects', 'projects.id', '=', 'project_books.project_id')
            ->select('projects.user_id', DB::raw('COUNT(DISTINCT project_books.id) as book_count'))
            ->groupBy('projects.user_id')
            ->pluck('book_count', 'projects.user_id');

        $salesTotals = DB::table('project_books as books')
            ->join('projects', 'projects.id', '=', 'books.project_id')
            ->leftJoin('project_book_sales as sales', 'sales.project_book_id', '=', 'books.id')
            ->whereBetween('sales.date', [$periodStart, $periodEnd])
            ->select('projects.user_id', DB::raw('SUM(sales.amount) as total_sales'))
            ->groupBy('projects.user_id')
            ->pluck('total_sales', 'projects.user_id');

        $costTotals = DB::table('project_registrations as registrations')
            ->join('projects', 'projects.id', '=', 'registrations.project_id')
            ->leftJoin('storage_distribution_costs as costs', 'costs.project_book_id', '=', 'registrations.id')
            ->where('registrations.field', 'central-distribution')
            ->whereBetween('costs.date', [$periodStart, $periodEnd])
            ->select('projects.user_id', DB::raw('SUM(costs.amount) as total_costs'))
            ->groupBy('projects.user_id')
            ->pluck('total_costs', 'projects.user_id');

        $payouts = RoyaltyPayout::where('year', $year)
            ->where('quarter', $quarter)
            ->get()
            ->keyBy('user_id');

        $overview = $authors->map(function ($author) use ($bookCounts, $salesTotals, $costTotals, $payouts) {
            $sales = (float) ($salesTotals[$author->id] ?? 0);
            $costs = (float) ($costTotals[$author->id] ?? 0) * 1.2;
            $payout = $sales - $costs;
            $payoutEntry = $payouts->get($author->id);

            return [
                'author' => $author,
                'book_count' => (int) ($bookCounts[$author->id] ?? 0),
                'sales' => $sales,
                'costs' => $costs,
                'payout' => $payout,
                'is_paid' => $payoutEntry ? (bool) $payoutEntry->is_paid : false,
            ];
        });

        return view('backend.royalties.index', compact('overview', 'year', 'quarter'));
    }

    public function show($userId, Request $request)
    {
        [$year, $quarter, $periodStart, $periodEnd] = $this->resolvePeriod($request);

        $author = User::findOrFail($userId);
        $projects = Project::where('user_id', $userId)->with('book')->get();
        $projectIds = $projects->pluck('id')->all();
        $bookIds = $projects->pluck('book.id')->filter()->all();

        $formatRecords = ProjectRegistration::from('project_registrations as cd')
            ->join(DB::raw("(
                SELECT MIN(id) as id, value, type, project_id
                FROM project_registrations
                WHERE field = 'ISBN'
                GROUP BY value, project_id
            ) as isbn"), function ($join) {
                $join->on('cd.value', '=', 'isbn.value')
                    ->on('cd.project_id', '=', 'isbn.project_id');
            })
            ->where('cd.field', 'central-distribution')
            ->whereIn('cd.project_id', $projectIds)
            ->select('cd.project_id', 'isbn.type as type_of_isbn')
            ->get()
            ->groupBy('project_id');

        $isbnTypes = (new ProjectRegistration)->isbnTypes();

        $bookSales = ProjectBookSale::query()
            ->whereIn('project_book_id', $bookIds)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->select('project_book_id', DB::raw('SUM(amount) as total_sales'))
            ->groupBy('project_book_id')
            ->pluck('total_sales', 'project_book_id');

        $bookCosts = DB::table('project_registrations as registrations')
            ->leftJoin('storage_distribution_costs as costs', 'costs.project_book_id', '=', 'registrations.id')
            ->where('registrations.field', 'central-distribution')
            ->whereIn('registrations.project_id', $projectIds)
            ->whereBetween('costs.date', [$periodStart, $periodEnd])
            ->select('registrations.project_id', DB::raw('SUM(costs.amount) as total_costs'))
            ->groupBy('registrations.project_id')
            ->pluck('total_costs', 'registrations.project_id');

        $books = $projects->map(function ($project) use ($formatRecords, $isbnTypes, $bookSales, $bookCosts) {
            $book = $project->book;
            $formats = collect($formatRecords->get($project->id, []))
                ->map(function ($record) use ($isbnTypes) {
                    return $isbnTypes[$record->type_of_isbn] ?? 'Ukjent format';
                })
                ->unique()
                ->values();

            $sales = $book ? (float) ($bookSales[$book->id] ?? 0) : 0;
            $costs = (float) ($bookCosts[$project->id] ?? 0) * 1.2;

            return [
                'title' => $book?->book_name ?? $project->name,
                'formats' => $formats,
                'sales' => $sales,
                'costs' => $costs,
                'payout' => $sales - $costs,
            ];
        });

        $totalSales = $books->sum('sales');
        $totalCosts = $books->sum('costs');
        $totalPayout = $books->sum('payout');

        $payoutEntry = RoyaltyPayout::where('user_id', $author->id)
            ->where('year', $year)
            ->where('quarter', $quarter)
            ->first();

        return view('backend.royalties.show', compact(
            'author',
            'books',
            'year',
            'quarter',
            'totalSales',
            'totalCosts',
            'totalPayout',
            'payoutEntry'
        ));
    }

    public function storePayout($userId, Request $request): RedirectResponse
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'quarter' => 'required|integer|min:1|max:4',
        ]);

        RoyaltyPayout::updateOrCreate([
            'user_id' => $userId,
            'year' => $request->input('year'),
            'quarter' => $request->input('quarter'),
        ], [
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return redirect()->back()->with([
            'errors' => \AdminHelpers::createMessageBag('Royalty payout marked as paid.'),
            'alert_type' => 'success',
        ]);
    }

    private function resolvePeriod(Request $request): array
    {
        $year = (int) $request->input('year', now()->year);
        $quarter = (int) $request->input('quarter', (int) ceil(now()->month / 3));

        $startMonth = ($quarter - 1) * 3 + 1;
        $periodStart = Carbon::create($year, $startMonth, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->addMonths(3)->subDay()->endOfDay();

        return [$year, $quarter, $periodStart, $periodEnd];
    }
}
