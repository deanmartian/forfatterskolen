<?php

namespace App\Http\Requests\Api\V1;

class SignedUploadRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'filename' => ['required', 'string', 'max:255', 'regex:/^[^\/\\\\]+$/'],
            'mime_type' => ['required', 'string', 'max:255'],
            'size' => ['required', 'integer', 'min:1', 'max:26214400'],
        ];
    }
}
