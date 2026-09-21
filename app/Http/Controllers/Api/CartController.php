<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\Beverage;
use App\Models\CartItem;
use App\Models\FoodItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return $this->cartResponse($request->user());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'     => ['required', 'in:food,beverage'],
            'id'       => ['required', 'integer'],
            'quantity' => ['sometimes', 'integer', 'between:1,20'],
        ]);

        $model   = $data['type'] === 'food' ? FoodItem::class : Beverage::class;
        $product = $model::findOrFail($data['id']);
        $this->ensureAvailable($product);
        $cartItem = $request->user()->cartItems()->firstOrNew([
            'purchasable_type' => $data['type'],
            'purchasable_id'   => $product->id,
        ]);

        $newQuantity = ($cartItem->exists ? $cartItem->quantity : 0) + ($data['quantity'] ?? 1);
        $this->ensureStock($product, $newQuantity);
        $cartItem->quantity = $newQuantity;
        $cartItem->save();
        return $this->cartResponse($request->user(), 201);
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_if($cartItem->user_id !== $request->user()->id, 404);
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'between:1,20'],
        ]);

        $product = $cartItem->purchasable;
        if (! $product || $product->trashed()) {
            throw ValidationException::withMessages([
                'id' => ['This item is no longer available.'],
            ]);
        }
        $this->ensureAvailable($product);
        $this->ensureStock($product, $data['quantity']);
        $cartItem->update(['quantity' => $data['quantity']]);
        return $this->cartResponse($request->user());
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_if($cartItem->user_id !== $request->user()->id, 404);
        $cartItem->delete();
        return $this->cartResponse($request->user());
    }

    public function clear(Request $request): JsonResponse
    {
        $request->user()->cartItems()->delete();
        return $this->cartResponse($request->user());
    }

    private function ensureAvailable($product): void
    {
        if (! $product->status || $product->quantity < 1) {
            throw ValidationException::withMessages([
                'id' => ['This item is not available right now.'],
            ]);
        }
    }

    private function ensureStock($product, int $quantity): void
    {
        if ($quantity > $product->quantity) {
            throw ValidationException::withMessages([
                'quantity' => ["Only {$product->quantity} left in stock."],
            ]);
        }
    }

    private function cartResponse(User $user, int $status = 200): JsonResponse
    {
        $items = $user->cartItems()->with('purchasable')->latest()->get();
        $total = $items->sum(function ($item) {
            $product = $item->purchasable;
            return ($product && ! $product->trashed() && $product->status)
                ? (float) $product->price * $item->quantity
                : 0;
        });

        return response()->json([
            'items'       => CartItemResource::collection($items)->resolve(),
            'items_count' => (int) $items->sum('quantity'),
            'total'       => round($total, 2),
        ], $status);
    }
}
