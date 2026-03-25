<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
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
        $rules = [
            'email' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'payment_mode_id' => 'required',
            'payment_plan_id' => 'required',
            'package_id' => 'required',
        ];

        // Innloggede brukere trenger ikke sende navn og terms separat
        if (!\Auth::check()) {
            $rules['first_name'] = 'required';
            $rules['last_name'] = 'required';
            $rules['agree_terms'] = 'required|accepted';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if (\Auth::check()) {
            $this->merge([
                'email' => $this->email ?: \Auth::user()->email,
                'first_name' => $this->first_name ?: \Auth::user()->first_name,
                'last_name' => $this->last_name ?: \Auth::user()->last_name,
            ]);
        }
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'street.required' => 'Gate navn er nødvendig',
            'city.required' => 'Poststed er nødvendig',
            'zip.required' => 'Postnummer er nødvendig',
        ];
    }
}
