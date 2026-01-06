<?php

namespace App\Modules\UserProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\UserProfile\Models\UserProfile;
use App\Modules\UserProfile\Requests\UpdateProfileRequest;
use App\Modules\UserProfile\Resources\UserProfileResource;
use App\Modules\UserProfile\Services\UserProfileService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Storage;

class UserProfileController extends Controller
{
    use ApiResponse, AuthorizesRequests;
    protected $service;
    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }
    public function show(User $user)
    {
        $profile = $this->service->getProfile($user);

        if (!$profile) {
            return $this->success(null, 'Profile tidak ditemukan');
        }

        Gate::authorize('view', $profile);

        return $this->success(new UserProfileResource($profile));
    }
    public function store(User $user, UpdateProfileRequest $request)
    {
        $profile = $this->service->saveProfile(
            $user,
            $request->validated(),
            $request->allFiles()
        );
        $this->authorize('create', [UserProfile::class]);
        return $this->success(new UserProfileResource($profile), 'Profile pengguna berhasil diperbarui');
    }
    public function downloadCv(User $user)
    {
        $profile = $this->service->getProfile($user);

        if (!$profile)
            abort(404);

        $this->authorize('view', $profile);

        $path = $this->service->getFilePath($profile->id, 'cv', true);

        if (!$path || !Storage::exists($path)) {
            abort(404, 'File CV belum diunggah.');
        }

        return Storage::download($path);
    }
}
