<?php

namespace App\Http\Requests\Api\V1;

class ShopManuscriptCheckoutStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'payment_plan_id' => ['required', 'integer', 'exists:payment_plans,id'],
            'email' => ['nullable', 'email'],
            'zip' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            'genre' => ['required', 'integer', 'exists:genre,id'],
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
