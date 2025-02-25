<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ProjectRegistration;

class StorageBookController extends Controller {

    public function index()
    {
        $projectCentralDistributions = ProjectRegistration::join('project_books', 
            'project_registrations.project_id', '=', 'project_books.project_id')
            ->select('project_registrations.*', 'book_name')
        ->where([
            'field' => 'central-distribution',
            'project_registrations.in_storage' => 1
        ])
        ->get();
        
        return view('backend.storage-books.index', compact('projectCentralDistributions'));
    }

}