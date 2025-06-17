<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\TimeRegister;
use App\TimeRegisterUsed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TimeRegisterController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'date' => 'required',
        ]);

        $model = $request->id ? TimeRegister::find($request->id) : new TimeRegister;
        $model->user_id = $request->learner_id;
        $model->project_id = $request->project_id;
        $model->date = $request->date;
        $model->time = $request->time;
        $model->time_used = $request->time_used;
        $model->notes = $request->notes;

        if ($request->hasFile('invoice_file') && $request->file('invoice_file')->isValid()) {
            $destinationPath = 'storage/time-register-invoice/'; // upload path

            $extension = pathinfo($_FILES['invoice_file']['name'], PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['invoice_file']['name'], PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);
            $filePath = $destinationPath.end($expFileName);
            $request->invoice_file->move($destinationPath, end($expFileName));
            $model->invoice_file = $filePath;
        }

        $model->description = is_null($request->description) || $request->description === 'null' ? null : $request->description;
        $model->save();

        $time = TimeRegister::find($model->id)->load('project');
        $message = $request->id ? 'Time register updated' : 'Time register added';
        if ($request->ajax()) {
            return response()->json($time);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag($message),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function destroy($id, Request $request)
    {

        $timeRegister = TimeRegister::find($id);
        $timeRegister->delete();
        if (! $request->ajax()) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Time Register deleted successfully'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }
        /* return response()->json(); */
    }

    public function timeUsedList($time_register_id): JsonResponse
    {
        $timeUsed = TimeRegisterUsed::where('time_register_id', $time_register_id)->get();

        return response()->json($timeUsed);
    }

    public function saveTimeUsed($time_register_id, Request $request)
    {

        $request->validate([
            'date' => 'required',
            'time_used' => 'required',
        ]);

        $model = $request->time_used_id ? TimeRegisterUsed::find($request->time_used_id) : new TimeRegisterUsed;
        $model->time_register_id = $time_register_id;
        $model->date = $request->date;
        $model->time_used = $request->time_used;
        $model->description = $request->description;
        $model->save();

        return $this->timeUsedList($time_register_id);
    }

    public function deleteTimeUsed($time_used_id, Request $request): RedirectResponse
    {
        $timeUsed = TimeRegisterUsed::find($time_used_id)->delete();
        if (! $request->ajax()) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Time used deleted successfully'),
                'alert_type' => 'success',
                'not-former-courses' => true,
            ]);
        }
    }
}
