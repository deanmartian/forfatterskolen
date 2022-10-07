<?php

namespace App\Http\Controllers\Backend;


use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\TimeRegister;
use Illuminate\Http\Request;

class TimeRegisterController extends Controller
{

    public function save( Request $request )
    {
        $this->validate($request, [
            'date' => 'required'
        ]);

        $model = $request->id ? TimeRegister::find($request->id) : new TimeRegister();
        $model->user_id = $request->learner_id;
        $model->project_id = $request->project_id;
        $model->date = $request->date;
        $model->time = $request->time;
        $model->time_used = $request->time_used;

        if ($request->hasFile('invoice_file') && $request->file('invoice_file')->isValid()) :
            $destinationPath = 'storage/time-register-invoice/'; // upload path

            $extension = pathinfo($_FILES['invoice_file']['name'],PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['invoice_file']['name'],PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

            $expFileName = explode('/', $fileName);
            $filePath = $destinationPath.end($expFileName);
            $request->invoice_file->move($destinationPath, end($expFileName));
            $model->invoice_file = $filePath;
        endif;

        $model->description = is_null($request->description) || $request->description === 'null' ? NULL : $request->description;
        $model->save();

        $time = TimeRegister::find($model->id)->load('project');
        return response()->json($time);
    }

    public function destroy( $id )
    {

        $timeRegister = TimeRegister::find($id);
        $timeRegister->delete();

        return response()->json();
    }

}