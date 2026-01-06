<?php

namespace App\Modules\UserProfile\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Modules\Project\Models\Project;
use App\Modules\UserProfile\Services\UserProfileService;
use App\Modules\UserProfile\Resources\UserProfileResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Log;

class PublicProfileController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }

    public function getHome()
    {
        $profile = $this->service->getHomeData();

        if (!$profile) {
            return $this->error('Valid active profile not found', 404);
        }

        return $this->success(new UserProfileResource($profile), 'Home data retrieved successfully');
    }

    public function streamPhoto($id)
    {
        $path = $this->service->getFilePath($id, 'photo');

        if (!$path || !Storage::exists($path)) {
            abort(404);
        }

        return Storage::response($path);
    }

    public function downloadCv($id)
    {
        $path = $this->service->getFilePath($id, 'cv', false);

        if (!$path || !Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }
    public function streamProjectThumbnail($id)
    {
        $project = Project::findOrFail($id);
        Log::info($id);
        Log::info($project->toArray());

        if (!$project->thumbnail_path || !Storage::exists($project->thumbnail_path)) {
            abort(404);
        }

        return Storage::response($project->thumbnail_path);
    }
}
