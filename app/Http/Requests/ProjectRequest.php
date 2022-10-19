<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'number' => 'required|unique:projects,identifier,' . \Request::instance()->id
        ];
    }

}