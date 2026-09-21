<?php

namespace App\Http\Resources;

use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->favorable;
        return [
            'id'         => $this->id,
            'type'       => $this->favorable_type,
            'item'       => $item
                ? ($item instanceof FoodItem ? new FoodItemResource($item) : new BeverageResource($item))
                : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
