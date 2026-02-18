<?php

namespace App\Http\Controllers\Api\V1;

use App\UserBookForSale;
use App\UserBookSale;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookSaleController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $books = UserBookForSale::where('user_id', $user->id)
            ->with('project')
            ->withCount('sales')
            ->get();

        return response()->json([
            'data' => $books->map(function ($book) {
                return $this->formatBookForSale($book);
            })->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = UserBookForSale::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['project', 'sales', 'detail', 'inventory', 'distributionCosts'])
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatBookForSaleDetailed($book),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'ebook_isbn' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $book = UserBookForSale::create([
            'user_id' => $user->id,
            'project_id' => $data['project_id'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'ebook_isbn' => $data['ebook_isbn'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
        ]);

        return response()->json([
            'message' => 'Book created.',
            'data' => $this->formatBookForSale($book),
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = UserBookForSale::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted.']);
    }

    public function salesByMonth(Request $request, int $year): JsonResponse
    {
        $user = $this->apiUser($request);

        $sales = UserBookSale::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->get()
            ->groupBy(function ($sale) {
                return Carbon::parse($sale->date)->format('m');
            });

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthlySales = $sales->get($key, collect());
            $monthlyData[] = [
                'month' => $m,
                'month_name' => Carbon::create($year, $m, 1)->format('F'),
                'total_quantity' => $monthlySales->sum('quantity'),
                'total_amount' => $monthlySales->sum(function ($s) {
                    return $s->quantity * $s->amount;
                }),
            ];
        }

        return response()->json([
            'year' => $year,
            'data' => $monthlyData,
        ]);
    }

    public function monthlyDetails(Request $request, int $year, int $month): JsonResponse
    {
        $user = $this->apiUser($request);

        $sales = UserBookSale::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('book')
            ->orderBy('date')
            ->get();

        return response()->json([
            'year' => $year,
            'month' => $month,
            'data' => $sales->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'date' => $sale->date,
                    'sale_type' => $sale->sale_type,
                    'sale_type_text' => $sale->sale_type_text,
                    'quantity' => $sale->quantity,
                    'amount' => $sale->amount,
                    'total_amount' => $sale->total_amount,
                    'book' => $sale->book ? [
                        'id' => $sale->book->id,
                        'title' => $sale->book->title,
                        'isbn' => $sale->book->isbn,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    private function formatBookForSale(UserBookForSale $book): array
    {
        return [
            'id' => $book->id,
            'isbn' => $book->isbn,
            'ebook_isbn' => $book->ebook_isbn,
            'title' => $book->title,
            'description' => $book->description,
            'price' => $book->price,
            'price_formatted' => $book->price_formatted,
            'project' => $book->project ? [
                'id' => $book->project->id,
                'name' => $book->project->name,
            ] : null,
            'sales_count' => $book->sales_count ?? 0,
        ];
    }

    private function formatBookForSaleDetailed(UserBookForSale $book): array
    {
        $base = $this->formatBookForSale($book);
        $base['sales'] = $book->sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'date' => $sale->date,
                'sale_type' => $sale->sale_type,
                'sale_type_text' => $sale->sale_type_text,
                'quantity' => $sale->quantity,
                'amount' => $sale->amount,
                'total_amount' => $sale->total_amount,
            ];
        })->values();
        $base['total_distribution_cost'] = $book->totalDistributionCost();

        return $base;
    }
}
