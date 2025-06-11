<?php

namespace App\Http\Controllers\Backend;

use App\CheckoutLog;

class CheckoutLogController
{
    public function index()
    {
        $logs = CheckoutLog::whereHas('user')->latest()->paginate(25);

        return view('backend.checkout-log.index', compact('logs'));
    }
}
