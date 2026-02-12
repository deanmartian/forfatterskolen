<?php

namespace App\Http\Requests\Api\V1;

class ShopManuscriptWordCountLookupRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'word_count' => ['required', 'integer', 'min:1'],
        ];
    }
}
