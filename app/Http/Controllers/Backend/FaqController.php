<?php

namespace App\Http\Controllers\Backend;

use App\Faq;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:10');
    }

    public function index(): View
    {
        $faqs = Faq::orderBy('created_at', 'asc')->get();

        return view('backend.faq.index', compact('faqs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);
        Faq::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Faq created successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function update($id, Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);
        $faq = Faq::findOrFail($id);
        $faq->title = $request->title;
        $faq->description = $request->description;
        $faq->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Faq updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $faq = Faq::findOrFail($id);
        $faq->forceDelete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Faq deleted successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }
}
