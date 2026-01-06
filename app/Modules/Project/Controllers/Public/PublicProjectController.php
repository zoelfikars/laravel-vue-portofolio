<?php

namespace App\Modules\Project\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Modules\Project\Resources\ProjectListResource;
use App\Modules\Project\Resources\ProjectResource;
use App\Traits\ApiResponse;

use App\Modules\Project\Services\ProjectService;
use App\Modules\UserProfile\Services\UserProfileService;
use Illuminate\Http\Request;

class PublicProjectController extends Controller
{
    use ApiResponse;

    protected $projectService;
    protected $profileService;

    public function __construct(ProjectService $projectService, UserProfileService $profileService)
    {
        $this->projectService = $projectService;
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        $user = $this->profileService->getActiveUser();

        if (!$user) {
            return $this->error('Active user not found', 404);
        }

        $projects = $this->projectService->listPublic($user, $request->all());

        return $this->success(ProjectListResource::collection($projects));
    }

    public function show($slug)
    {
        $user = $this->profileService->getActiveUser();

        if (!$user) {
            return $this->error('Active user not found', 404);
        }

        $project = $this->projectService->getBySlug($user, $slug);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        return $this->success(new ProjectResource($project));
    }
}
