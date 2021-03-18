<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AssignmentManuscript;
use App\ShopManuscriptsTaken;
use Illuminate\Support\Facades\Auth;
use App\CorrectionManuscript;
use App\CopyEditingManuscript;

class HeadEditorController extends Controller
{
    public function index(){
        $assignedAssignmentManuscripts = AssignmentManuscript::where('status', 0) //pending
        ->where('has_feedback', 1)
        ->whereHas('assignment', function($query) {
            $query->where('parent', 'users');
        })
        ->get();
        $assigned_shop_manuscripts = ShopManuscriptsTaken::get();
        $assigned_shop_manuscripts = $assigned_shop_manuscripts->filter(function($model){
            return $model->status == 'Pending';
        });
        $assignedAssignments = AssignmentManuscript::where('status', 0) //pending
        ->where('has_feedback', 1)
        ->whereHas('assignment', function($query){
            $query->whereNull('parent');
        })
        ->get();
        $corrections = CorrectionManuscript::where('status', 3)->get();
        $copyEditings = CopyEditingManuscript::where('status', 3)->get();
        return view('backend.head-editor.index', compact('assignedAssignmentManuscripts', 'assigned_shop_manuscripts', 'assignedAssignments','corrections', 'copyEditings'));
    }
}
