<?php

namespace App\Http\Controllers\Backend;


use App\CheckoutLog;

class CheckoutLogController
{

    public function index()
    {
        $logs = CheckoutLog::with('user')->paginate(25);
        return view('backend.checkout-log.index', compact('logs'));
    }

}