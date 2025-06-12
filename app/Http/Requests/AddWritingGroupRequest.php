<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWritingGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'contact_id' => 'required|exists:users,id',
            'description' => 'required|string',
        ];
    }
}
