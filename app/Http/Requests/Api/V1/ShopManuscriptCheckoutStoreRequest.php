<?php

namespace App\Http\Requests\Api\V1;

class ShopManuscriptCheckoutStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120'],
            'genre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'coaching_time_later' => ['nullable', 'boolean'],
            'send_to_email' => ['nullable', 'boolean'],
            'manuscript' => ['required', 'file', 'mimes:docx,pdf,doc,odt'],
            'synopsis' => ['nullable', 'file', 'mimes:pdf,doc,docx,odt'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idempotency_key' => (string) $this->header('Idempotency-Key', ''),
            'coaching_time_later' => $this->boolean('coaching_time_later'),
            'send_to_email' => $this->boolean('send_to_email'),
        ]);
    }
}
