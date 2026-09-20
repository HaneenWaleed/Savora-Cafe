<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BeverageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => 'beverage',
            'name'         => $this->name,
            'description'  => $this->description,
            'price'        => (float) $this->price,
            'category_id'  => $this->category_id,
            'category'     => CategoryResource::make($this->whenLoaded('category')),
            'size'         => $this->size,
            'ingredients'  => $this->ingredients ?? [],
            'calories'     => $this->calories,
            'temperature'  => $this->temperature,
            'quantity'     => $this->quantity,
            'image_url'    => $this->image ? asset('storage/' . $this->image) : null,
            'status'       => $this->status,
            'is_available' => $this->status && $this->quantity > 0,
        ];
    }
}
