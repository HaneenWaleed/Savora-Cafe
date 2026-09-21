<?php

namespace App\Http\Resources;

use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->purchasable;
        $available = $product
            && ! $product->trashed()
            && $product->status
            && $product->quantity >= $this->quantity;
        return [
            'id'           => $this->id,
            'type'         => $this->purchasable_type,
            'quantity'     => $this->quantity,
            'is_available' => (bool) $available,
            'unit_price'   => $product ? (float) $product->price : null,
            'line_total'   => $product ? round((float) $product->price * $this->quantity, 2) : null,
            'product'      => $product
                ? ($product instanceof FoodItem ? new FoodItemResource($product) : new BeverageResource($product))
                : null,
        ];
    }
}
