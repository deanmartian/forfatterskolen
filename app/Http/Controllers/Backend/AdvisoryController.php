<?php

namespace App\Http\Controllers\Backend;

use App\Advisory;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdvisoryController extends Controller
{
    /**
     * Update the advisory details
     */
    public function update($id, Request $request): RedirectResponse
    {
        $advisory = Advisory::find($id);
        $request->validate([
            'from_date' => 'required',
            'advisory' => 'required',
        ]);
        $updateData = $request->except('_token');
        if (isset($updateData['pageList'])) {
            $updateData['page_included'] = serialize($updateData['pageList']);
        }
        $advisory->update($updateData);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Advisory updated successfully.'),
            'alert_type' => 'success']);
    }
}
