<?php

namespace App\Services;

use App\Models\Beverage;
use App\Models\CustomerPreference;
use App\Models\FoodItem;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationEngine
{
    private const WEIGHTS = [
        'category'          => 20,
        'liked_ingredient'   => 20,
        'disliked_ingredient' => -30,
        'taste'              => 15,
        'spicy'              => 10,
        'price'              => 15,
        'previous_orders'    => 20,
    ];

    public function forUser(User $user, string $type = 'all', int $limit = 10, ?int $excludeId = null): array
    {
        $preference = $user->preference;
        $foodItems  = $type !== 'beverage' ? FoodItem::available()->with('category')->get() : collect();
        $beverages  = $type !== 'food' ? Beverage::available()->with('category')->get() : collect();
        $items      = $foodItems->concat($beverages);
        if ($excludeId) {
            $items = $items->reject(fn ($item) => $item->id === $excludeId);
        }
        $orderedIds = $this->previouslyOrderedIds($user);
        return $items
            ->map(fn ($item) => $this->score($item, $preference, $orderedIds))
            ->sortByDesc('match_percentage')
            ->take($limit)
            ->values()
            ->all();
    }

    public function scoreSingle($item, ?CustomerPreference $preference, User $user): array
    {
        return $this->score($item, $preference, $this->previouslyOrderedIds($user));
    }

    private function score($item, ?CustomerPreference $preference, Collection $orderedIds): array
    {
        if (! $preference) {
            return $this->result($item, 50, ['reason' => 'No preferences set yet.']);
        }
        $points  = 0;
        $reasons = [];
        $ingredients = collect($item->ingredients ?? [])->map(fn ($i) => mb_strtolower($i));

        // 1) category
        if (in_array($item->category_id, $preference->favorite_categories ?? [])) {
            $points += self::WEIGHTS['category'];
            $reasons[] = "You like {$item->category->name}";
        }

        // 2) likedIngredients
        $likedIngredients = collect($preference->favorite_ingredients ?? []);
        $matchedLiked     = $ingredients->intersect($likedIngredients);
        if ($matchedLiked->isNotEmpty() && $likedIngredients->isNotEmpty()) {
            $share = self::WEIGHTS['liked_ingredient'] * ($matchedLiked->count() / min($likedIngredients->count(), 3));
            $points += min($share, self::WEIGHTS['liked_ingredient']);
            $reasons[] = 'Contains ' . $matchedLiked->implode(', ');
        }

        // 3) disliked taste
        $dislikedIngredients = collect($preference->disliked_ingredients ?? []);
        $matchedDisliked     = $ingredients->intersect($dislikedIngredients);
        if ($matchedDisliked->isNotEmpty()) {
            $points += self::WEIGHTS['disliked_ingredient'];
            $reasons[] = 'Contains ' . $matchedDisliked->implode(', ') . ' which you dislike';
        }

        // 4) perfect test
        if ($preference->preferred_taste === 'spicy' && $item->spicy_level >= 3) {
            $points += self::WEIGHTS['taste'];
            $reasons[] = 'Matches your spicy taste preference';
        } elseif ($preference->preferred_taste && $preference->preferred_taste !== 'spicy' && $item->spicy_level === 0) {
            $points += self::WEIGHTS['taste'] * 0.5;
        }

        // 5) tempreture
        $itemSpicy = $item instanceof FoodItem ? $item->spicy_level : 0;
        $spicyDiff = abs($itemSpicy - $preference->spicy_level);
        $points += self::WEIGHTS['spicy'] * max(0, 1 - $spicyDiff / 5);

        // 6) budget
        $price = (float) $item->price;
        $withinBudget = match ($preference->price_preference) {
            'low'    => $price <= 80,
            'medium' => $price > 50 && $price <= 150,
            'high'   => $price > 100,
            default  => true,
        };

        if ($withinBudget) {
            $points += self::WEIGHTS['price'];
            $reasons[] = 'Fits your budget';
        }

        // 7) previous order of the same product
        $key = $item instanceof FoodItem ? "food_{$item->id}" : "beverage_{$item->id}";

        if ($orderedIds->contains($key)) {
            $points += self::WEIGHTS['previous_orders'];
            $reasons[] = 'You ordered this before';
        }
        $percentage = max(0, min(100, round($points)));
        return $this->result($item, $percentage, ['reasons' => $reasons]);
    }

    private function result($item, int $percentage, array $meta): array
    {
        return [
            'item'             => $item,
            'match_percentage' => $percentage,
            'reasons'          => $meta['reasons'] ?? [$meta['reason'] ?? ''],
        ];
    }

    private function previouslyOrderedIds(User $user): Collection
    {
        return OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id)->where('status', '!=', 'cancelled'))
            ->get(['orderable_type', 'orderable_id'])
            ->map(fn ($row) => "{$row->orderable_type}_{$row->orderable_id}")
            ->unique();
    }
}
