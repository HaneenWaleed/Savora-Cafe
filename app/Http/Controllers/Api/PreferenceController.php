<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePreferenceRequest;
use App\Http\Resources\PreferenceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PreferenceController extends Controller
{
    private const TEXT_LISTS = [
        'favorite_food_types',
        'favorite_beverages',
        'favorite_ingredients',
        'disliked_ingredients',
    ];

    public function show(Request $request): JsonResponse
    {
        $preference = $request->user()->preference;
        return response()->json([
            'preference' => $preference ? new PreferenceResource($preference) : null,
        ]);
    }

    public function update(UpdatePreferenceRequest $request): JsonResponse
    {
        $user       = $request->user();
        $preference = $user->preference;
        $data       = $request->validated();

        foreach (self::TEXT_LISTS as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $this->normalize($data[$key]);
            }
        }

        foreach (['favorite_categories', 'dietary_preferences'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = array_values($data[$key] ?? []);
            }
        }

        $liked    = $data['favorite_ingredients'] ?? $preference?->favorite_ingredients ?? [];
        $disliked = $data['disliked_ingredients'] ?? $preference?->disliked_ingredients ?? [];
        $overlap  = array_intersect($liked, $disliked);

        if (! empty($overlap)) {
            throw ValidationException::withMessages([
                'disliked_ingredients' => ['These ingredients are in both lists: ' . implode(', ', $overlap)],
            ]);
        }

        $preference = $user->preference()->updateOrCreate(['user_id' => $user->id], $data);

        return response()->json([
            'message'    => 'Preferences saved.',
            'preference' => new PreferenceResource($preference),
        ]);
    }

    private function normalize(?array $values): array
    {
        return collect($values ?? [])
            ->map(fn ($value) => mb_strtolower(trim($value)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
