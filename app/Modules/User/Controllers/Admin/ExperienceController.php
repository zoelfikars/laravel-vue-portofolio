<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\Models\Experience;
use App\Modules\User\Requests\ExperienceRequest;
use App\Modules\User\Resources\ExperienceResource;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Modules\User\Services\ExperienceService;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    protected $experienceService;

    public function __construct(ExperienceService $experienceService)
    {
        $this->experienceService = $experienceService;
    }

    public function index(Request $request, User $user)
    {
        $experiences = $this->experienceService->list($user, $request->all());
        return $this->success(ExperienceResource::collection($experiences));
    }

    public function store(User $user, ExperienceRequest $request)
    {
        $this->authorize('create', [Experience::class, $user]);
        $experience = $this->experienceService->create($user, $request->validated());
        return $this->success(new ExperienceResource($experience), 'Experience added successfully', 201);
    }

    public function update(Experience $experience, ExperienceRequest $request)
    {
        $this->authorize('update', $experience);
        $experience = $this->experienceService->update($experience, $request->validated());
        return $this->success(new ExperienceResource($experience), 'Experience updated successfully');
    }

    public function destroy(Experience $experience)
    {
        $this->authorize('delete', $experience);
        $this->experienceService->delete($experience);
        return $this->success(null, 'Experience deleted successfully');
    }
}
