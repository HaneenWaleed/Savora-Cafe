<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeverageRequest;
use App\Http\Resources\BeverageResource;
use App\Models\Beverage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeverageController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id'  => ['nullable', 'integer'],
            'min_price'    => ['nullable', 'numeric', 'min:0'],
            'max_price'    => ['nullable', 'numeric', 'min:0'],
            'max_calories' => ['nullable', 'integer', 'min:0'],
            'temperature'  => ['nullable', 'in:hot,cold'],
            'size'         => ['nullable', 'in:small,medium,large'],
            'sort'         => ['nullable', 'in:name,newest,price_asc,price_desc'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $isAdmin = $request->user('sanctum')?->isAdmin() ?? false;

        $query = Beverage::query()->with('category');

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
            ->when($request->filled('temperature'), fn ($q) => $q->where('temperature', $request->temperature))
            ->when($request->filled('size'), fn ($q) => $q->where('size', $request->size))
            ->when($request->filled('ingredient'), fn ($q) => $q->whereJsonContains('ingredients', strtolower($request->ingredient)))
            ->when($request->boolean('available'), fn ($q) => $q->where('quantity', '>', 0));

        match ($request->get('sort')) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest'     => $query->latest(),
            default      => $query->orderBy('name'),
        };

        return BeverageResource::collection(
            $query->paginate($request->integer('per_page', 12))->withQueryString()
        );
    }

    public function show(Request $request, Beverage $beverage)
    {
        $isAdmin = $request->user('sanctum')?->isAdmin() ?? false;
        abort_if(! $isAdmin && ! $beverage->status, 404);
        return new BeverageResource($beverage->load('category'));
    }
    public function store(BeverageRequest $request): JsonResponse
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $beverage = Beverage::create($data);
        return (new BeverageResource($beverage->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(BeverageRequest $request, Beverage $beverage)
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            if ($beverage->image) {
                Storage::disk('public')->delete($beverage->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }
        $beverage->update($data);
        return new BeverageResource($beverage->fresh('category'));
    }
    public function destroy(Beverage $beverage): JsonResponse
    {
        $beverage->delete();
        return response()->json(['message' => 'Beverage deleted.']);
    }
}
