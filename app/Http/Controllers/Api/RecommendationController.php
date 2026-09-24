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

    public function surprise(Request $request)
    {
        $request->validate([
            'type' => ['nullable', 'in:all,food,beverage'],
        ]);

        $top = $this->engine->forUser($request->user(), $request->get('type', 'all'), 10);

        abort_if(empty($top), 404, 'No available items right now.');

        return new RecommendedItemResource($top[array_rand($top)]);
    }

    public function healthy(Request $request)
    {
        $request->validate([
            'type'  => ['nullable', 'in:all,food,beverage'],
            'limit' => ['nullable', 'integer', 'between:1,20'],
        ]);

        $results = collect($this->engine->forUser($request->user(), $request->get('type', 'all'), 100))
            ->filter(fn ($r) => $r['item']->calories !== null)
            ->sortBy(fn ($r) => $r['item']->calories)
            ->take($request->integer('limit', 10))
            ->values()
            ->all();

        return RecommendedItemResource::collection($results);
    }
}
