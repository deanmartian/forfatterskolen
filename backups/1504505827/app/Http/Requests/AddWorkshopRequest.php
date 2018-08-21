<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWorkshopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'duration' => 'required|integer',
            'seats' => 'required|integer',
            'location' => 'required|string',
            'fiken_product' => 'required|string',
        ];
    }
}
