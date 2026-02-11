<?php

namespace App\Http\Requests\Api\V1;

class ShopManuscriptCheckoutStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idempotency_key' => (string) $this->header('Idempotency-Key', ''),
        ]);
    }
}
