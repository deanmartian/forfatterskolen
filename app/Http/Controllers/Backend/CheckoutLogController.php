<?php

namespace App\Http\Controllers\Backend;

use Illuminate\View\View;
use App\CheckoutLog;

class CheckoutLogController
{
    public function index(): View
    {
        $logs = CheckoutLog::whereHas('user')->latest()->paginate(25);

        return view('backend.checkout-log.index', compact('logs'));
    }
}
