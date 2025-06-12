<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AddWorkshopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(Request $request): array
    {
        // check if the workshop is free and use this rule
        if ($request->is_free) {
            return [
                'title' => 'required|string',
                'date' => 'required|date',
                'duration' => 'required|integer',
                'seats' => 'required|integer',
                'location' => 'required|string',
                'fiken_product' => 'required|string',
            ];
        }

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
