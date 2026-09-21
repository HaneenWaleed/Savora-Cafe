<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Beverage;
use App\Models\FoodItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['type' => ['nullable', 'in:food,beverage']]);
        $favorites = $request->user()->favorites()
            ->when($request->filled('type'), fn ($q) => $q->where('favorable_type', $request->type))
            ->with('favorable.category')
            ->latest()
            ->get()
            ->filter(fn ($favorite) => $favorite->favorable && $favorite->favorable->status)
            ->values();
        return FavoriteResource::collection($favorites);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:food,beverage'],
            'id'   => ['required', 'integer'],
        ]);
        $model   = $data['type'] === 'food' ? FoodItem::class : Beverage::class;
        $product = $model::where('status', true)->findOrFail($data['id']);
        $favorite = $request->user()->favorites()->firstOrCreate([
            'favorable_type' => $data['type'],
            'favorable_id'   => $product->id,
        ]);
        return (new FavoriteResource($favorite->load('favorable.category')))
            ->response()
            ->setStatusCode($favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, string $type, int $id): JsonResponse
    {
        $deleted = $request->user()->favorites()
            ->where('favorable_type', $type)
            ->where('favorable_id', $id)
            ->delete();
        if (! $deleted) {
            return response()->json(['message' => 'This item is not in your favorites.'], 404);
        }
        return response()->json(['message' => 'Removed from favorites.']);
    }
}
