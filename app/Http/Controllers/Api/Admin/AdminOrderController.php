<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // الحالات المسموح الانتقال ليها
    private const TRANSITIONS = [
        'pending'   => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready'     => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function index(Request $request)
    {
        $request->validate([
            'status'         => ['nullable', 'in:pending,preparing,ready,completed,cancelled'],
            'payment_status' => ['nullable', 'in:unpaid,paid,refunded'],
            'date'           => ['nullable', 'date'],
            'per_page'       => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $orders = Order::query()
            ->with(['user', 'items.orderable'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['user', 'items.orderable']));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status'         => ['required', 'in:pending,preparing,ready,completed,cancelled'],
            'payment_status' => ['sometimes', 'in:unpaid,paid,refunded'],
        ]);

        $newStatus = $data['status'];

        if ($newStatus !== $order->status) {
            $allowed = self::TRANSITIONS[$order->status] ?? [];

            if (! in_array($newStatus, $allowed, true)) {
                return response()->json([
                    'message' => "Cannot change status from '{$order->status}' to '{$newStatus}'.",
                ], 422);
            }

            if ($newStatus === 'cancelled') {
                $order->cancel(); // بترجّع المخزون
            } else {
                $order->update(['status' => $newStatus]);
            }
        }

        if (isset($data['payment_status'])) {
            $order->update(['payment_status' => $data['payment_status']]);
        }

        return response()->json([
            'message' => 'Order updated.',
            'order'   => new OrderResource($order->fresh()->load(['user', 'items.orderable'])),
        ]);
    }
}