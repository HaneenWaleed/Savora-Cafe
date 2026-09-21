<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodItemRequest;
use App\Http\Resources\FoodItemResource;
use App\Models\FoodItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodItemController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id'  => ['nullable', 'integer'],
            'min_price'    => ['nullable', 'numeric', 'min:0'],
            'max_price'    => ['nullable', 'numeric', 'min:0'],
            'max_calories' => ['nullable', 'integer', 'min:0'],
            'spicy_level'  => ['nullable', 'integer', 'between:0,5'],
            'sort'         => ['nullable', 'in:name,newest,price_asc,price_desc'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $isAdmin = $request->user('sanctum')?->isAdmin() ?? false;
        $query = FoodItem::query()->with('category');
        if (! $isAdmin) {
            $query->where('status', true);
        }

        $query
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(function ($w) use ($term) {
                    $w->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('ingredients', 'like', $term);
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->max_price))
            ->when($request->filled('max_calories'), fn ($q) => $q->where('calories', '<=', $request->max_calories))
            ->when($request->filled('spicy_level'), fn ($q) => $q->where('spicy_level', $request->spicy_level))
            ->when($request->filled('ingredient'), fn ($q) => $q->whereJsonContains('ingredients', strtolower($request->ingredient)))
            ->when($request->boolean('available'), fn ($q) => $q->where('quantity', '>', 0));
        match ($request->get('sort')) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->latest(),
            default      => $query->orderBy('name'),
        };

        return FoodItemResource::collection(
            $query->paginate($request->integer('per_page', 12))->withQueryString()
        );
    }

    public function show(Request $request, FoodItem $foodItem)
    {
        $isAdmin = $request->user('sanctum')?->isAdmin() ?? false;
        abort_if(! $isAdmin && ! $foodItem->status, 404);
        return new FoodItemResource($foodItem->load('category'));
    }

    public function store(FoodItemRequest $request): JsonResponse
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $foodItem = FoodItem::create($data);
        return (new FoodItemResource($foodItem->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(FoodItemRequest $request, FoodItem $foodItem)
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            if ($foodItem->image) {
                Storage::disk('public')->delete($foodItem->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $foodItem->update($data);
        return new FoodItemResource($foodItem->fresh('category'));
    }
    public function destroy(FoodItem $foodItem): JsonResponse
    {
        $foodItem->delete(); // Soft delete: الأوردرات القديمة تفضل سليمة
        return response()->json(['message' => 'Food item deleted.']);
    }
}
