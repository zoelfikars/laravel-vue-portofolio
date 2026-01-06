<?php

namespace App\Modules\User\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Modules\User\Resources\ExperienceResource;
use App\Traits\ApiResponse;

use App\Modules\User\Services\ExperienceService;
use App\Modules\UserProfile\Services\UserProfileService;
use Illuminate\Http\Request;

class PublicExperienceController extends Controller
{
    use ApiResponse;

    protected $experienceService;
    protected $profileService;

    public function __construct(ExperienceService $experienceService, UserProfileService $profileService)
    {
        $this->experienceService = $experienceService;
        $this->profileService = $profileService;
    }
    public function index(Request $request)
    {
        $user = $this->profileService->getActiveUser();

        if (!$user) {
            return $this->error('Active user not found', 404);
        }

        $experiences = $this->experienceService->listPublic($user, $request->all());

        return $this->success(ExperienceResource::collection($experiences));
    }
}
