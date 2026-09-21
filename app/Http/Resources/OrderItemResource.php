<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->orderable;
        return [
            'id'        => $this->id,
            'type'      => $this->orderable_type,
            'item_id'   => $this->orderable_id,
            'name'      => $product?->name,
            'image_url' => $product?->image ? asset('storage/' . $product->image) : null,
            'quantity'  => $this->quantity,
            'price'     => (float) $this->price,
            'subtotal'  => (float) $this->subtotal,
        ];
    }
}
