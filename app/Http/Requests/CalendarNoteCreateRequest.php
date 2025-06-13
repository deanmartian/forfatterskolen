<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarNoteCreateRequest extends FormRequest
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
            'note' => 'required',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ];
    }
}
