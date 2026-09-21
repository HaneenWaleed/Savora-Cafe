<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'role'     => ['nullable', 'in:admin,customer'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $users = User::query()
            ->withCount('orders')
            ->withSum(['orders' => fn ($q) => $q->sales()], 'total_price')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(function ($w) use ($term) {
                    $w->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();
        return UserResource::collection($users);
    }

    public function show(User $user): JsonResponse
    {
        $user->loadCount('orders')
            ->loadSum(['orders' => fn ($q) => $q->sales()], 'total_price')
            ->load('preference');
        $recentOrders = $user->orders()->with('items.orderable')->latest()->limit(5)->get();
        return response()->json([
            'user'          => new UserResource($user),
            'recent_orders' => OrderResource::collection($recentOrders)->resolve(),
        ]);
    }

    public function store(AdminUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'customer',
            'phone'    => $data['phone'] ?? null,
            'age'      => $data['age'] ?? null,
        ]);
        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(AdminUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        if ($user->id === $request->user()->id && isset($data['role']) && $data['role'] !== 'admin') {
            return response()->json(['message' => 'You cannot remove your own admin role.'], 422);
        }
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return response()->json([
            'message' => 'User updated.',
            'user'    => new UserResource($user),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        if ($user->orders()->exists()) {
            return response()->json(['message' => 'This user has orders and cannot be deleted.'], 409);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }
}
