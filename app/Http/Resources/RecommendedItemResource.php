<?php

namespace App\Http\Resources;

use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommendedItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this['item'];
        return [
            'match_percentage' => $this['match_percentage'],
            'reasons'          => array_values(array_filter($this['reasons'])),
            'item'             => $item instanceof FoodItem
                ? new FoodItemResource($item)
                : new BeverageResource($item),
        ];
    }
}
