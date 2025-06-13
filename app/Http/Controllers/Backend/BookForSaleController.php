<?php

namespace App\Http\Controllers\Backend;

use AdminHelpers;
use App\Http\Controllers\Controller;
use App\StorageDistributionCost;
use App\StorageInventory;
use App\StorageSale;
use App\User;
use App\UserBookForSale;
use App\UserBookSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookForSaleController extends Controller
{
    public function index(): View
    {
        $books = UserBookForSale::paginate(25);
        $learners = User::where('role', 2)->with(['projects.registrations' => function ($query) {
            $query->where('field', 'isbn');
        }])->get();

        return view('backend.book-for-sale.index', compact('books', 'learners'));
    }

    public function show($id): View
    {
        $book = UserBookForSale::find($id);
        $totalBookSold = $book->sales()->sum('quantity');
        $totalBookSale = $book->sales()->sum('amount');

        $quantitySoldCount = $this->salesReportCounter($id, 'quantity-sold');
        $turnedOverCount = $this->salesReportCounter($id, 'turned-over');
        $freeCount = $this->salesReportCounter($id, 'free');
        $commissionCount = $this->salesReportCounter($id, 'commission');
        $shreddedCount = $this->salesReportCounter($id, 'shredded');
        $defectiveCount = $this->salesReportCounter($id, 'defective');
        $correctionsCount = $this->salesReportCounter($id, 'corrections');
        $countsCount = $this->salesReportCounter($id, 'counts');
        $returnsCount = $this->salesReportCounter($id, 'returns');

        $bookSale = new UserBookSale;
        $bookSaleTypes = $bookSale->saleTypes();

        return view('backend.book-for-sale.show', compact('book', 'totalBookSold', 'totalBookSale',
            'quantitySoldCount', 'turnedOverCount', 'freeCount', 'commissionCount', 'shreddedCount',
            'defectiveCount', 'correctionsCount', 'countsCount', 'returnsCount', 'bookSaleTypes'));
    }

    public function saveInventory($book_for_sale_id, Request $request)
    {
        StorageInventory::updateOrCreate([
            'user_book_for_sale_id' => $book_for_sale_id,
        ], $request->except('id'));

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Inventory saved successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function saveSales($book_for_sale_id, Request $request)
    {
        StorageSale::updateOrCreate([
            'id' => $request->id,
            'user_book_for_sale_id' => $book_for_sale_id,
        ], $request->except('id'));

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Sales updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function saleDetails($book_for_sale_id, Request $request): JsonResponse
    {
        $details = StorageSale::where('user_book_for_sale_id', $book_for_sale_id)
            ->where('type', $request->type)
            ->get();

        return response()->json([
            'details' => $details,
        ]);
    }

    public function deleteSales($storage_sales_id)
    {
        StorageSale::find($storage_sales_id)->delete();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Sales report deleted successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function saveDistributionCost($book_for_sale_id, Request $request)
    {
        StorageDistributionCost::updateOrCreate([
            'id' => $request->id,
            'user_book_for_sale_id' => $book_for_sale_id,
        ], $request->except('id'));

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Distribution Cost saved successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function deleteDistributionCost($distribution_cost_id)
    {
        StorageDistributionCost::find($distribution_cost_id)->delete();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Distribution cost deleted successfully.'),
            'alert_type' => 'success',
        ]);
    }

    private function salesReportCounter($book_for_sale_id, $type)
    {
        return StorageSale::where('user_book_for_sale_id', $book_for_sale_id)
            ->where('type', $type)
            ->sum('value');
    }
}
