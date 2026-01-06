<?php

namespace App\Modules\Project\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\Project\Requests\ProjectRequest;
use App\Modules\Project\Resources\ProjectResource;
use App\Modules\Project\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index(Request $request, User $user)
    {
        $projects = $this->projectService->list($user, $request->all());
        return $this->success(ProjectResource::collection($projects));
    }
    public function store(ProjectRequest $request, User $user)
    {
        $this->authorize('create', Project::class);
        $project = $this->projectService->createProject($user, $request->validated());
        return $this->success(new ProjectResource($project), 'Project created successfully', 201);
    }
    public function show($id)
    {
        $project = $this->projectService->getById($id);
        $this->authorize('view', $project);
        return $this->success(new ProjectResource($project));
    }
    public function update(ProjectRequest $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);
        $project = $this->projectService->updateProject($project, $request->validated());
        return $this->success(new ProjectResource($project), 'Project updated successfully');
    }
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);
        $this->projectService->deleteProject($project);
        return $this->success(null, 'Project deleted successfully');
    }
}
