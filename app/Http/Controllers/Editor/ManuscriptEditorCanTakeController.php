<?php

namespace App\Http\Controllers\Editor;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\ManuscriptEditorCanTake;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ManuscriptEditorCanTakeController extends Controller
{
    public function index(): View
    {
        $manuscriptEditorCanTake = ManuscriptEditorCanTake::where('editor_id', Auth::user()->id)
            ->orderBy('date_from', 'asc')
            ->get();

        return view('editor.how-many-manuscript-you-can-take', compact('manuscriptEditorCanTake'));
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $message = '';

        if ($request->id) {

            $manuscriptEditorCanTake = ManuscriptEditorCanTake::find($request->id);
            $manuscriptEditorCanTake->update($data);
            $message = 'Record updated successfully.';

        } else {

            $data['editor_id'] = Auth::user()->id;
            ManuscriptEditorCanTake::create($data);
            $message = 'Record saved successfully.';

        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag($message),
            'alert_type' => 'success']);
    }

    public function delete($id): RedirectResponse
    {
        $manuscriptEditorCanTake = ManuscriptEditorCanTake::find($id);
        $manuscriptEditorCanTake->delete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record Successfully Deleted.'),
            'alert_type' => 'success']);
    }
}
