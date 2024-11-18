<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ProjectRegistration;

class StorageBookController extends Controller {

    public function index()
    {
        $projectCentralDistributions = ProjectRegistration::leftJoin('project_books', 
            'project_registrations.project_id', '=', 'project_books.project_id')
        ->where([
            'field' => 'central-distribution',
            'project_registrations.in_storage' => 1
        ])
        ->get();
        
        return view('backend.storage-books.index', compact('projectCentralDistributions'));
    }

}