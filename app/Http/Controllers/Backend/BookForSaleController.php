<?php

namespace App\Http\Controllers\Backend;

use AdminHelpers;
use App\Http\Controllers\Controller;
use App\StorageInventory;
use App\StorageSale;
use App\UserBookForSale;
use Illuminate\Http\Request;

class BookForSaleController extends Controller
{
    
    public function index()
    {
        $books = UserBookForSale::paginate(25);

        return view('backend.book-for-sale.index', compact('books'));
    }

    public function show($id)
    {
        $book = UserBookForSale::find($id);
        $totalBookSold = $book->sales()->sum('quantity');
        $totalBookSale = $book->sales()->sum('amount');

        return view('backend.book-for-sale.show', compact('book', 'totalBookSold', 'totalBookSale'));
    }

    public function saveInventory($book_for_sale_id, Request $request) 
    {
        StorageInventory::updateOrCreate([
            'user_book_for_sale_id' => $book_for_sale_id
        ], $request->except('id'));

        return back()->with([
            'errors'                => AdminHelpers::createMessageBag('Inventory saved successfully.'),
            'alert_type'            => 'success'
        ]);
    }

    public function saveSales($book_for_sale_id, Request $request) 
    {
        StorageSale::updateOrCreate([
            'id' => $request->id,
            'user_book_for_sale_id' => $book_for_sale_id
        ], $request->except('id'));
        
        return back()->with([
            'errors'                => AdminHelpers::createMessageBag('Sales updated successfully.'),
            'alert_type'            => 'success'
        ]);
    }

    public function saleDetails($book_for_sale_id, Request $request)
    {
        $details = StorageSale::where('user_book_for_sale_id', $book_for_sale_id)
            ->where('type', $request->type)
            ->get();

        return response()->json([
            'details' => $details
        ]);
    }

}
