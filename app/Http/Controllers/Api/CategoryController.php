<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['type' => ['nullable', 'in:food,beverage']]);

        $categories = Category::query()
            ->withCount(['foodItems', 'beverages'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category->loadCount(['foodItems', 'beverages']));
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $category = Category::create($data);

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return new CategoryResource($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $hasItems = $category->foodItems()->withTrashed()->exists()
            || $category->beverages()->withTrashed()->exists();

        if ($hasItems) {
            return response()->json([
                'message' => 'Cannot delete a category that still has items.',
            ], 409);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }
}
