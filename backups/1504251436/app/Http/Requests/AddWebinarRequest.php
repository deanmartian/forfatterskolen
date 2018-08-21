<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWebinarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
        ];
    }
}
