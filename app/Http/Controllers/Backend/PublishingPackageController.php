<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublishingPackageController extends Controller
{
    
    public function services()
    {
        return view('backend.publishing-package.services');
    }

}
