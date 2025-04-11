<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class FundResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'starting_balance' => $this->starting_balance,
            'current_value' => $this->current_value,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'returns_count' => $this->whenCounted('returns'),
        ];
    }
}