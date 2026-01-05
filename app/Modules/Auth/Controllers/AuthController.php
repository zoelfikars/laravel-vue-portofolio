<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->validated());

            return $this->success($data, 'Login berhasil');
        } catch (ValidationException $e) {
            return $this->error('Email atau password salah', 401, $e->errors());
        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan saat login', 500, $e->getMessage());
        }
    }
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logout berhasil');
    }
    public function user(Request $request): JsonResponse
    {
        $user = $this->authService->user($request->user());
        return $this->success($user, 'Berhasil mengambil data user');
    }
}
