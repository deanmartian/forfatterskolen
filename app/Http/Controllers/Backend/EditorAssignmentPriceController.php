<?php

namespace App\Http\Controllers\Backend;

use App\EditorAssignmentPrices;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditorAssignmentPriceController extends Controller
{
    public function save(Request $request): RedirectResponse
    {

        $data = $request->except('_token');
        $message = '';

        if ($request->id) {
            $editorAssignmentPrices = EditorAssignmentPrices::find($request->id);
            // $editorAssignmentPrices->update($data);
            $editorAssignmentPrices->update([
                'price' => $request->price,
            ]);
            $message = 'Record Updated Successfuly';
        } else {
            EditorAssignmentPrices::create($data);
            $message = 'Record Saved Successfuly';
        }

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag($message),
            'alert_type' => 'success']);

    }

    public function delete($id): RedirectResponse
    {

        $editorAssignmentPrices = EditorAssignmentPrices::find($id);
        $editorAssignmentPrices->delete();

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Record Successfully Deleted.'),
            'alert_type' => 'success']);

    }
}
