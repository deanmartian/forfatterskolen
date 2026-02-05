<?php

namespace App\Http\Requests\Api\V1;

class ProfileUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['prohibited'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'array'],
            'address.street' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['nullable', 'string', 'max:20'],
            'address.city' => ['nullable', 'string', 'max:255'],
        ];
    }
}
