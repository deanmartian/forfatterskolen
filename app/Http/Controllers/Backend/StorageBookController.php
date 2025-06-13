<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\ProjectRegistration;
use DB;
use Illuminate\View\View;

class StorageBookController extends Controller
{
    public function index(): View
    {
        /* $projectCentralDistributions = ProjectRegistration::join('project_books',
            'project_registrations.project_id', '=', 'project_books.project_id')
            ->select('project_registrations.*', 'book_name')
        ->where([
            'field' => 'central-distribution',
            'project_registrations.in_storage' => 1
        ])
        ->get(); */

        $projectCentralDistributions = ProjectRegistration::from('project_registrations as cd')
            ->join(DB::raw("
                (
                    SELECT MIN(id) as id, value, type, project_id, book_price
                    FROM project_registrations
                    WHERE field = 'ISBN'
                    GROUP BY value, project_id
                ) as isbn
            "), function ($join) {
                $join->on('cd.value', '=', 'isbn.value')
                    ->on('cd.project_id', '=', 'isbn.project_id');
            })
            ->join('project_books', 'cd.project_id', '=', 'project_books.project_id')
            ->where('cd.field', 'central-distribution')
            ->where('cd.in_storage', 1)
            ->select('cd.*', 'project_books.book_name', 'isbn.type as type_of_isbn', 'isbn.book_price as isbn_book_price')
            ->get();

        $isbnTypes = (new ProjectRegistration)->isbnTypes();

        return view('backend.storage-books.index', compact('projectCentralDistributions', 'isbnTypes'));
    }
}
