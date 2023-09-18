<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;

class BookPublisherController extends Controller
{

    public function calculator()
    {
        return view('backend.book-publisher.calculator');
    }

}