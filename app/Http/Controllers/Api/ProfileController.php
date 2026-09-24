<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    private function resolveUser(Request $request): ?User
    {
        $user = $request->user();
        if ($user) {
            return $user;
        }

        $email = trim((string) ($request->header('X-User-Email') ?: $request->input('user_email') ?: ''));
        if ($email === '') {
            return null;
        }

        return User::firstOrCreate(
            ['email' => strtolower($email)],
            [
                'name' => ucfirst(str_replace(['.', '_', '-'], ' ', explode('@', $email)[0])) ?: 'Customer',
                'password' => Hash::make(Str::random(16)),
                'role' => 'customer',
            ]
        );
    }

    public function show(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'user' => new UserResource($user->load('preference')),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->update($request->validated());
        return response()->json([
            'message' => 'Profile updated.',
            'user'    => new UserResource($user->fresh()->load('preference')),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);
        $user = $request->user();
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }
        $user->update(['password' => Hash::make($data['password'])]);
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();
        return response()->json(['message' => 'Password updated.']);
    }
}
