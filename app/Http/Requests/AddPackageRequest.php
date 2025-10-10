<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPackageRequest extends FormRequest
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
            'variation' => 'required',
            'description' => 'required',
            'manuscripts_count' => 'required|integer|min:0',
            'full_payment_price' => 'required|numeric|min:0',
            'is_standard' => 'nullable|boolean',
            /* 'months_3_price' => 'required|numeric|min:0',
            'months_6_price' => 'required|numeric|min:0', */
            'full_price_product' => 'required|string|max:255',
            /* 'months_3_product' => 'required|string|max:255',
            'months_6_product' => 'required|string|max:255', */
            'full_price_due_date' => 'required|integer|min:0',
            /* 'months_3_due_date' => 'required|integer|min:0',
            'months_6_due_date' => 'required|integer|min:0', */
        ];
    }
}
