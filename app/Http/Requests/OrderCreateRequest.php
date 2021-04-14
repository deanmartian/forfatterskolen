<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
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
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'payment_mode_id' => 'required',
            'payment_plan_id' => 'required',
            'package_id' => 'required',
            'agree_terms' => 'required|accepted'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'street.required' => 'Gate navn er nødvendig',
            'city.required'  => 'Poststed er nødvendig',
            'zip.required'  => 'Postnummer er nødvendig',
        ];
    }
}
