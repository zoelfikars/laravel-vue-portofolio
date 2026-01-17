<?php

namespace App\Modules\UserProfile\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\UserProfile\Models\UserProfile;
use App\Modules\UserProfile\Requests\UpdateProfileRequest;
use App\Modules\UserProfile\Resources\UserProfileResource;
use App\Modules\UserProfile\Services\UserProfileService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

        $this->authorize('view', $profile);

        return $this->success(new UserProfileResource($profile));
    }
    public function store(User $user, UpdateProfileRequest $request)
    {
        $this->authorize('create', [UserProfile::class]);

        $profile = $this->service->saveProfile(
            $user,
            $request->validated(),
            $request->allFiles()
        );
        return $this->success(new UserProfileResource($profile), 'Profile pengguna berhasil diperbarui');
    }
    public function streamCv(User $user)
    {
        $profile = $this->service->getProfile($user);

        if (!$profile)
            abort(404);

        $this->authorize('view', $profile);

        $path = $this->service->verifyCvPath($profile);

        if (!$path) {
            abort(404, 'File CV belum diunggah.');
        }

        $disk = config('filesystems.default');
        return Storage::disk($disk)->response($path, null, [
            'Content-Type' => 'application/pdf', // Paksa tipe PDF (opsional, storage biasanya otomatis)
            'Content-Disposition' => 'inline; filename="cv.pdf"' // 'inline' membuat browser membukanya
        ]);
    }
}
