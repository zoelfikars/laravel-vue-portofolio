<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\Models\Experience;
use App\Modules\User\Requests\ExperienceRequest;
use App\Modules\User\Resources\ExperienceResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Gate;

class ExperienceController extends Controller
{
    use ApiResponse;

    public function index(User $user)
    {
        $experiences = $user->experiences()
            ->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();

        return $this->success(ExperienceResource::collection($experiences));
    }

    public function store(User $user, ExperienceRequest $request)
    {
        if (!Gate::allows('create', [Experience::class, $user])) {
            return $this->error('Unauthorized', 403);
        }

        $data = $request->validated();

        if ($data['is_current'] ?? false) {
            $data['end_date'] = null;
        }

        $experience = $user->experiences()->create($data);

        return $this->success(new ExperienceResource($experience), 'Experience added successfully', 201);
    }

    public function update(Experience $experience, ExperienceRequest $request)
    {
        if (!Gate::allows('update', $experience)) {
            return $this->error('Unauthorized', 403);
        }

        $data = $request->validated();

        if ($data['is_current'] ?? false) {
            $data['end_date'] = null;
        }

        $experience->update($data);

        return $this->success(new ExperienceResource($experience), 'Experience updated successfully');
    }

    public function destroy(Experience $experience)
    {
        if (!Gate::allows('delete', $experience)) {
            return $this->error('Unauthorized', 403);
        }

        $experience->delete();

        return $this->success(null, 'Experience deleted successfully');
    }
}
