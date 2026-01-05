<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Services\UserService;
use App\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->list($request->all());
        return $this->success(UserResource::collection($users), 'Data user berhasil diambil');
    }
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->create($request->validated());

            return $this->success(new UserResource($user), 'User berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->error('Gagal membuat user', 500, $e->getMessage());
        }
    }
    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user), 'Detail user berhasil diambil');
    }
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $user = $this->userService->update($user, $request->validated());

            return $this->success(new UserResource($user), 'User berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->error('Gagal memperbarui user', 500, $e->getMessage());
        }
    }
    public function destroy(User $user): JsonResponse
    {
        try {
            $this->userService->delete($user);

            return $this->success(null, 'User berhasil dihapus');
        } catch (\Exception $e) {
            return $this->error('Gagal menghapus user', 500, $e->getMessage());
        }
    }
}
