<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Beverage;
use App\Models\FoodItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('items.orderable')
            ->latest()
            ->paginate(10);
        return OrderResource::collection($orders);
    }

    public function show(Request $request, int $order)
    {
        $order = $request->user()->orders()
            ->with('items.orderable')
            ->findOrFail($order);
        return new OrderResource($order);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        $user      = $request->user();
        $cartItems = $user->cartItems()->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }
        $order = DB::transaction(function () use ($user, $cartItems, $data) {
            $order = $user->orders()->create([
                'total_price' => 0,
                'notes'       => $data['notes'] ?? null,
            ]);
            $total = 0;
            foreach ($cartItems as $cartItem) {
                $model = $cartItem->purchasable_type === 'food' ? FoodItem::class : Beverage::class;
                $product = $model::whereKey($cartItem->purchasable_id)->lockForUpdate()->first();
                if (! $product || ! $product->status || $product->quantity < $cartItem->quantity) {
                    $label = $product?->name ?? 'An item';
                    throw ValidationException::withMessages([
                        'cart' => ["{$label} is no longer available in the requested quantity."],
                    ]);
                }

                $price    = (float) $product->price;
                $subtotal = round($price * $cartItem->quantity, 2);
                $product->decrement('quantity', $cartItem->quantity);
                $order->items()->create([
                    'orderable_type' => $cartItem->purchasable_type,
                    'orderable_id'   => $product->id,
                    'quantity'       => $cartItem->quantity,
                    'price'          => $price,
                    'subtotal'       => $subtotal,
                ]);
                $total += $subtotal;
            }
            $order->update(['total_price' => $total]);
            $user->cartItems()->delete();
            return $order;
        });
        return (new OrderResource($order->load('items.orderable')))
            ->response()
            ->setStatusCode(201);
    }

    public function cancel(Request $request, int $order): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($order);
        if (! $order->canBeCancelled()) {
            return response()->json([
                'message' => 'Only pending orders can be cancelled.',
            ], 422);
        }
        $order->cancel();
        return response()->json([
            'message' => 'Order cancelled.',
            'order'   => new OrderResource($order->fresh()->load('items.orderable')),
        ]);
    }

        public function destroy(Request $request, int $order): JsonResponse
        {
            $order = $request->user()->orders()->findOrFail($order);
            $order->delete();

            return response()->json([
                'message' => 'Order deleted.',
            ]);
        }

}
