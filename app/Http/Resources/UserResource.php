<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'age'          => $this->age,
            'role'         => $this->role,
            'orders_count' => $this->whenCounted('orders'),
            'total_spent'  => $this->when(
                array_key_exists('orders_sum_total_price', $this->resource->getAttributes()),
                fn () => round((float) $this->orders_sum_total_price, 2)
            ),
            'preference'   => PreferenceResource::make($this->whenLoaded('preference')),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
