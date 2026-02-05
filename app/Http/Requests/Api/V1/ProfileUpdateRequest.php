<?php

namespace App\Http\Requests\Api\V1;

class ProfileUpdateRequest extends ApiRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->isMethod('put') || $this->all() !== []) {
            return;
        }

        $contentType = $this->header('Content-Type', '');

        if (strpos($contentType, 'multipart/form-data') !== 0) {
            return;
        }

        $data = $this->parseMultipartFormData($contentType);

        if ($data !== []) {
            $this->merge($data);
        }
    }

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

    private function parseMultipartFormData(string $contentType): array
    {
        if (! preg_match('/boundary=(.*)$/', $contentType, $matches)) {
            return [];
        }

        $boundary = $matches[1];
        $content = $this->getContent();

        if ($content === '' || $content === false) {
            return [];
        }

        $parts = preg_split('/-+' . preg_quote($boundary, '/') . '/', $content);
        $data = [];

        foreach ($parts as $part) {
            $part = ltrim($part, "\r\n");

            if ($part === '' || $part === '--') {
                continue;
            }

            [$rawHeaders, $body] = array_pad(explode("\r\n\r\n", $part, 2), 2, '');

            if (! preg_match('/name=\"([^\"]+)\"/', $rawHeaders, $headerMatches)) {
                continue;
            }

            $name = $headerMatches[1];
            $value = rtrim($body, "\r\n");
            $parsed = [];

            parse_str($name.'='.urlencode($value), $parsed);

            $data = array_replace_recursive($data, $parsed);
        }

        return $data;
    }
}
