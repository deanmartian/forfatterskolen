<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use App\Http\Controllers\Controller;

class BookPublisherController extends Controller
{
    public function calculator(): View
    {
        return view('backend.book-publisher.calculator');
    }
}
