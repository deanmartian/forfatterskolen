<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'meta_title' => 'required|max:70|min:40',
            'meta_description' => 'required|max:160|min:70',
            'free_for_days' => [
                'required_if:is_free,on',
            ],
        ];
    }
}
