<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecommendedItemResource;
use App\Models\Beverage;
use App\Models\FoodItem;
use App\Services\RecommendationEngine;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationEngine $engine)
    {
    }

    public function index(Request $request)
    {
        $request->validate([
            'type'  => ['nullable', 'in:all,food,beverage'],
            'limit' => ['nullable', 'integer', 'between:1,20'],
        ]);
        $results = $this->engine->forUser(
            $request->user(),
            $request->get('type', 'all'),
            $request->integer('limit', 10)
        );
        return RecommendedItemResource::collection($results);
    }

    public function matchForFood(Request $request, FoodItem $foodItem)
    {
        $result = $this->engine->scoreSingle($foodItem->load('category'), $request->user()->preference, $request->user());
        return new RecommendedItemResource($result);
    }

    public function matchForBeverage(Request $request, Beverage $beverage)
    {
        $result = $this->engine->scoreSingle($beverage->load('category'), $request->user()->preference, $request->user());
        return new RecommendedItemResource($result);
    }
}
