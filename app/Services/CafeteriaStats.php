<?php

namespace App\Services;

use App\Models\Beverage;
use App\Models\Category;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CafeteriaStats
{
    public function overview(int $lowStockThreshold = 5): array
    {
        $today = Carbon::today();

        $byStatus = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $lowStockCount = FoodItem::where('status', true)->where('quantity', '<=', $lowStockThreshold)->count()
            + Beverage::where('status', true)->where('quantity', '<=', $lowStockThreshold)->count();

        return [
            'customers_count'  => User::where('role', 'customer')->count(),
            'new_customers_7d' => User::where('role', 'customer')->where('created_at', '>=', now()->subDays(7))->count(),
            'food_items_count' => FoodItem::count(),
            'beverages_count'  => Beverage::count(),
            'categories_count' => Category::count(),
            'orders_count'     => Order::count(),
            'orders_today'     => Order::whereDate('created_at', $today)->count(),
            'orders_by_status' => collect(['pending', 'preparing', 'ready', 'completed', 'cancelled'])
                ->mapWithKeys(fn ($status) => [$status => (int) ($byStatus[$status] ?? 0)])
                ->all(),
            'sales_total'      => round((float) Order::sales()->sum('total_price'), 2),
            'sales_today'      => round((float) Order::sales()->whereDate('created_at', $today)->sum('total_price'), 2),
            'low_stock_count'  => $lowStockCount,
        ];
    }

    public function topItems(string $type = 'all', int $limit = 5, ?int $days = null): array
    {
        $rows = $this->aggregatedItems($type, $days)
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        $foodNames = FoodItem::withTrashed()
            ->whereIn('id', $rows->where('orderable_type', 'food')->pluck('orderable_id'))
            ->pluck('name', 'id');

        $beverageNames = Beverage::withTrashed()
            ->whereIn('id', $rows->where('orderable_type', 'beverage')->pluck('orderable_id'))
            ->pluck('name', 'id');

        return $rows->map(fn ($row) => [
            'type'           => $row->orderable_type,
            'id'             => (int) $row->orderable_id,
            'name'           => ($row->orderable_type === 'food' ? $foodNames : $beverageNames)->get($row->orderable_id),
            'total_quantity' => (int) $row->total_quantity,
            'total_sales'    => round((float) $row->total_sales, 2),
        ])->values()->all();
    }

    public function categories(): array
    {
        $rows = $this->aggregatedItems()->get();

        $foodCategory = FoodItem::withTrashed()
            ->whereIn('id', $rows->where('orderable_type', 'food')->pluck('orderable_id'))
            ->pluck('category_id', 'id');

        $beverageCategory = Beverage::withTrashed()
            ->whereIn('id', $rows->where('orderable_type', 'beverage')->pluck('orderable_id'))
            ->pluck('category_id', 'id');

        $ordered = [];

        foreach ($rows as $row) {
            $categoryId = ($row->orderable_type === 'food' ? $foodCategory : $beverageCategory)->get($row->orderable_id);

            if ($categoryId) {
                $ordered[$categoryId] = ($ordered[$categoryId] ?? 0) + (int) $row->total_quantity;
            }
        }

        return Category::withCount(['foodItems', 'beverages'])->get()
            ->map(fn ($category) => [
                'id'               => $category->id,
                'name'             => $category->name,
                'type'             => $category->type,
                'items_count'      => $category->food_items_count + $category->beverages_count,
                'ordered_quantity' => $ordered[$category->id] ?? 0,
            ])
            ->sortByDesc('ordered_quantity')
            ->values()
            ->all();
    }

    public function neverOrdered(): array
    {
        $food = FoodItem::whereDoesntHave('orderItems')->orderBy('name')->get(['id', 'name'])
            ->map(fn ($item) => ['type' => 'food', 'id' => $item->id, 'name' => $item->name]);
        $beverages = Beverage::whereDoesntHave('orderItems')->orderBy('name')->get(['id', 'name'])
            ->map(fn ($item) => ['type' => 'beverage', 'id' => $item->id, 'name' => $item->name]);
        return $food->concat($beverages)->values()->all();
    }

    public function lowStock(int $threshold = 5): array
    {
        $food = FoodItem::where('status', true)->where('quantity', '<=', $threshold)->get(['id', 'name', 'quantity'])
            ->map(fn ($item) => ['type' => 'food', 'id' => $item->id, 'name' => $item->name, 'quantity' => $item->quantity]);
        $beverages = Beverage::where('status', true)->where('quantity', '<=', $threshold)->get(['id', 'name', 'quantity'])
            ->map(fn ($item) => ['type' => 'beverage', 'id' => $item->id, 'name' => $item->name, 'quantity' => $item->quantity]);
        return $food->concat($beverages)->sortBy('quantity')->values()->all();
    }

    public function salesByDay(int $days = 7): array
    {
        $start = Carbon::today()->subDays($days - 1);
        $rows = Order::sales()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as orders_count, SUM(total_price) as sales')
            ->groupBy('day')
            ->get()
            ->keyBy('day');
        $result = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $row  = $rows->get($date);
            $result[] = [
                'date'         => $date,
                'orders_count' => (int) ($row->orders_count ?? 0),
                'sales'        => round((float) ($row->sales ?? 0), 2),
            ];
        }

        return $result;
    }

    private function aggregatedItems(string $type = 'all', ?int $days = null)
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->when($type !== 'all', fn ($q) => $q->where('order_items.orderable_type', $type))
            ->when($days, fn ($q) => $q->where('orders.created_at', '>=', now()->subDays($days)))
            ->groupBy('order_items.orderable_type', 'order_items.orderable_id')
            ->select('order_items.orderable_type', 'order_items.orderable_id')
            ->selectRaw('SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_sales');
    }
}
