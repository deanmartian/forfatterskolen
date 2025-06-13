<?php

namespace App\Http\Controllers\Backend;

use App\EmailHistory;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EmailHistoryController extends Controller
{
    public function index(): View
    {
        $histories = EmailHistory::latest()->paginate(10);

        return view('backend.email-history.index', compact('histories'));
    }
}
