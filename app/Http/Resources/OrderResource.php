<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'payment_status'   => $this->payment_status,
            'total_price'      => (float) $this->total_price,
            'notes'            => $this->notes,
            'can_be_cancelled' => $this->canBeCancelled(),
            'created_at'       => $this->created_at?->toISOString(),
            'customer'         => UserResource::make($this->whenLoaded('user')),
            'items'            => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}