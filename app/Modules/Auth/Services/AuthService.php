<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\User\Resources\UserResource;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Modules\Auth\Resources\LoginResource;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class AuthService
{
    public function login(array $credentials): LoginResource
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }
        if (EnsureFrontendRequestsAreStateful::fromFrontend(request())) {
            Auth::guard('web')->login($user);
            request()->session()->regenerate();
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->token = $token;

        return new LoginResource($user);
    }
    public function logout(User $user): void
    {
        if (EnsureFrontendRequestsAreStateful::fromFrontend(request())) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        } else {
            $user = request()->user();
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
        }
    }
    public function user(User $user): UserResource
    {
        return new UserResource($user);
    }
}
