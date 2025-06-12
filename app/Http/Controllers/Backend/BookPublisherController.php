<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BookPublisherController extends Controller
{
    public function calculator(): View
    {
        return view('backend.book-publisher.calculator');
    }
}
