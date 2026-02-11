<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopManuscriptPlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'max_words' => (int) $this->max_words,
            'full_payment_price' => (float) $this->full_payment_price,
            'split_payment_price' => isset($this->split_payment_price) ? (float) $this->split_payment_price : null,
        ];
    }
}
