<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\CafeteriaStats;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function __construct(private CafeteriaStats $stats)
    {
    }

    public function overview(Request $request): JsonResponse
    {
        $request->validate(['threshold' => ['nullable', 'integer', 'min:0', 'max:1000']]);
        return response()->json([
            'data' => $this->stats->overview($request->integer('threshold', 5)),
        ]);
    }

    public function topItems(Request $request): JsonResponse
    {
        $request->validate([
            'type'  => ['nullable', 'in:all,food,beverage'],
            'limit' => ['nullable', 'integer', 'between:1,20'],
            'days'  => ['nullable', 'integer', 'between:1,365'],
        ]);

        return response()->json([
            'data' => $this->stats->topItems(
                $request->get('type', 'all'),
                $request->integer('limit', 5),
                $request->filled('days') ? $request->integer('days') : null
            ),
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json(['data' => $this->stats->categories()]);
    }

    public function neverOrdered(): JsonResponse
    {
        return response()->json(['data' => $this->stats->neverOrdered()]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $request->validate(['threshold' => ['nullable', 'integer', 'min:0', 'max:1000']]);
        return response()->json([
            'data' => $this->stats->lowStock($request->integer('threshold', 5)),
        ]);
    }

    public function sales(Request $request): JsonResponse
    {
        $request->validate(['days' => ['nullable', 'integer', 'between:1,90']]);
        return response()->json([
            'data' => $this->stats->salesByDay($request->integer('days', 7)),
        ]);
    }
}
