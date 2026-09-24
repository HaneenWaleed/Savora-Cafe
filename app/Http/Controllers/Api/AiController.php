<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BeverageResource;
use App\Http\Resources\FoodItemResource;
use App\Models\Beverage;
use App\Models\FoodItem;
use App\Services\AiHelper;
use App\Services\GrokClient;
use App\Services\RecommendationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class AiController extends Controller
{
    public function __construct(
        private GrokClient $ai,
        private AiHelper $helper,
        private RecommendationEngine $recommendations,
    ) {
    }

    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string', 'max:300'],
        ]);

        $menu = $this->helper->menuSummary();
        $system = <<<PROMPT
        You are a menu search engine for Savora Cafe. Given a natural-language query, find the most relevant items from the menu below.
        Menu:
        {$menu}

        Respond ONLY with valid JSON in this exact shape, no extra text:
        {"item_ids": [<numeric ids of matching items, best match first, max 8>], "explanation": "<one short sentence in the same language as the query>"}

        Only include ids that actually appear in the menu above. If nothing matches well, return an empty array.
        PROMPT;

        try {
            $result = $this->ai->generateJson($system, $data['query']);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        $ids = collect($result['item_ids'] ?? [])->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id);

        $foodItems = FoodItem::available()->with('category')->whereIn('id', $ids)->get();
        $beverages = Beverage::available()->with('category')->whereIn('id', $ids)->get();

        $ordered = $ids->map(function ($id) use ($foodItems, $beverages) {
            return $foodItems->firstWhere('id', $id) ?? $beverages->firstWhere('id', $id);
        })->filter()->values();

        return response()->json([
            'explanation' => $result['explanation'] ?? '',
            'results'     => $ordered->map(fn ($item) => $item instanceof FoodItem
                ? new FoodItemResource($item)
                : new BeverageResource($item)
            ),
        ]);
    }

    // POST /api/ai/compare — مقارنة صنفين أو أكتر
    public function compare(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items'          => ['required', 'array', 'min:2', 'max:4'],
            'items.*.type'   => ['required', 'in:food,beverage'],
            'items.*.id'     => ['required', 'integer'],
        ]);

        $items = collect($data['items'])->map(function ($ref) {
            $item = $ref['type'] === 'food'
                ? FoodItem::with('category')->find($ref['id'])
                : Beverage::with('category')->find($ref['id']);

            abort_if(! $item, 404, "Item {$ref['type']}#{$ref['id']} not found.");

            return $item;
        });

        $itemsText = $items->map(function ($item) {
            $spicy = $item instanceof FoodItem ? $item->spicy_level : 'N/A';
            $temp  = $item instanceof Beverage ? $item->temperature : 'N/A';

            return "id:{$item->id} name:\"{$item->name}\" price:{$item->price} calories:{$item->calories} spicy:{$spicy} temperature:{$temp} ingredients:[" . implode(',', $item->ingredients ?? []) . ']';
        })->implode("\n");

        $system = <<<PROMPT
        Compare the following menu items for a customer, using only the data given. Do not invent any facts.

        Items:
        {$itemsText}

        Respond ONLY with valid JSON in this exact shape, no extra text:
        {
          "summary": "<2-3 sentence comparison highlighting the key differences>",
          "cheapest_id": <id of the cheapest item>,
          "lowest_calorie_id": <id of the lowest calorie item, or null if unknown>,
          "spiciest_id": <id of the spiciest item, or null if not applicable>
        }
        PROMPT;

        try {
            $result = $this->ai->generateJson($system, 'Compare these items.');
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        return response()->json([
            'summary' => $result['summary'] ?? '',
            'items'   => $items->map(fn ($item) => $item instanceof FoodItem
                ? new FoodItemResource($item)
                : new BeverageResource($item)
            ),
            'cheapest_id'       => $result['cheapest_id'] ?? null,
            'lowest_calorie_id' => $result['lowest_calorie_id'] ?? null,
            'spiciest_id'       => $result['spiciest_id'] ?? null,
        ]);
    }

    // GET /api/ai/combo — اقتراح كومبو (وجبة + مشروب + حلو اختياري)
    public function combo(Request $request): JsonResponse
    {
        $user = $request->user();

        $topFood = collect($this->recommendations->forUser($user, 'food', 10))
            ->map(fn ($r) => $r['item'])
            ->take(10);

        $topBeverages = collect($this->recommendations->forUser($user, 'beverage', 10))
            ->map(fn ($r) => $r['item'])
            ->take(10);

        $desserts = FoodItem::available()->whereHas('category', fn ($q) => $q->where('slug', 'desserts'))->get();

        $itemsText = $topFood->concat($topBeverages)->concat($desserts)
            ->unique('id')
            ->map(fn ($item) => "id:{$item->id} type:" . ($item instanceof FoodItem ? 'food' : 'beverage') . " name:\"{$item->name}\" price:{$item->price}")
            ->implode("\n");

        $system = <<<PROMPT
        Suggest one meal combo (one food item + one beverage, and optionally one dessert) for this customer, using ONLY the items listed below. Pick items that pair well together (e.g. spicy food with a cooling drink).

        Available items:
        {$itemsText}

        Respond ONLY with valid JSON in this exact shape, no extra text:
        {
          "food_id": <id>,
          "beverage_id": <id>,
          "dessert_id": <id or null>,
          "reason": "<one short sentence explaining why this combo works well>"
        }
        PROMPT;

        try {
            $result = $this->ai->generateJson($system, 'Suggest a combo for me.');
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }

        $food     = FoodItem::with('category')->find($result['food_id'] ?? null);
        $beverage = Beverage::with('category')->find($result['beverage_id'] ?? null);
        $dessert  = FoodItem::with('category')->find($result['dessert_id'] ?? null);

        $total = ($food?->price ?? 0) + ($beverage?->price ?? 0) + ($dessert?->price ?? 0);

        return response()->json([
            'reason'  => $result['reason'] ?? '',
            'food'    => $food ? new FoodItemResource($food) : null,
            'beverage' => $beverage ? new BeverageResource($beverage) : null,
            'dessert' => $dessert ? new FoodItemResource($dessert) : null,
            'total_price' => round($total, 2),
        ]);
    }
}
