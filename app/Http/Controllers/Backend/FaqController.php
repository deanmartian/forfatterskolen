<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Faq;

class FaqController extends Controller
{

    /**
     * FaqController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:10');
    }

    public function index()
    {
    	$faqs = Faq::orderBy('created_at', 'asc')->get();
    	return view('backend.faq.index', compact('faqs'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|max:255',
    		'description' => 'required',
    	]);
    	Faq::create([
    		'title' => $request->title,
    		'description' => $request->description
    	]);

    	return redirect()->back();	
    }



    public function update($id, Request $request)
    {
    	$this->validate($request, [
    		'title' => 'required|max:255',
    		'description' => 'required',
    	]);
    	$faq = Faq::findOrFail($id);
    	$faq->title = $request->title;
    	$faq->description = $request->description;
    	$faq->save();

    	return redirect()->back();	
    }

    public function destroy($id, Request $request)
    {
    	$faq = Faq::findOrFail($id);
    	$faq->forceDelete();

    	return redirect()->back();	
    }
}