<?php

namespace App\Http\Controllers\Backend;


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
        $model->project = $request->project;
        $model->date = $request->date;
        $model->time = $request->time;
        $model->time_used = $request->time_used;
        $model->description = $request->description;
        $model->save();

        $time = TimeRegister::find($model->id);
        return response()->json($time);
    }

    public function destroy( $id )
    {

        $timeRegister = TimeRegister::find($id);
        $timeRegister->delete();

        return response()->json();
    }

}