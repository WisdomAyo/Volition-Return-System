<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FundReturnResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'fund_id' => $this->fund_id,
            'date' => $this->date->format('Y-m-d'),
            'frequency' => $this->frequency,
            'return_percentage' => $this->return_percentage,
            'is_compounding' => $this->is_compounding,
            'value_before' => $this->value_before,
            'value_after' => $this->value_after,
            'notes' => $this->notes,
            'reverted' => (bool) $this->reverted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}