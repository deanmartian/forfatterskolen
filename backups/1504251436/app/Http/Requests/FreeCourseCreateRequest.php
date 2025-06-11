<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FreeCourseCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'url' => 'required',
            'course_image' => 'required',
        ];
    }
}
