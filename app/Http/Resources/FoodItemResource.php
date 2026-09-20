<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'type'             => 'food',
            'name'             => $this->name,
            'description'      => $this->description,
            'price'            => (float) $this->price,
            'category_id'      => $this->category_id,
            'category'         => CategoryResource::make($this->whenLoaded('category')),
            'ingredients'      => $this->ingredients ?? [],
            'calories'         => $this->calories,
            'spicy_level'      => $this->spicy_level,
            'quantity'         => $this->quantity,
            'preparation_time' => $this->preparation_time,
            'image_url'        => $this->image ? asset('storage/' . $this->image) : null,
            'status'           => $this->status,
            'is_available'     => $this->status && $this->quantity > 0,
        ];
    }
}
