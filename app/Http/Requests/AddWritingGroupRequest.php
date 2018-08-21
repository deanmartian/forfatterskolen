<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddWritingGroupRequest extends FormRequest {
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'contact_id' => 'required|exists:users,id',
            'description' => 'required|string'
        ];
    }
}