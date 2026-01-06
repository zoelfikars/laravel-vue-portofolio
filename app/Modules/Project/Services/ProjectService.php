<?php

namespace App\Modules\Project\Services;

use App\Models\User;
use App\Modules\Project\Models\Project;
use App\Modules\User\Models\Skill;
use App\Traits\FileUpload;
use DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProjectService
{
    use FileUpload;
    public function list(User $user, array $params)
    {
        $query = $user->projects()
            ->with('techStack')
            ->latest();

        if (isset($params['search']) && $params['search']) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhereHas('techStack', function ($q) use ($search) {
                        $q->where('name', 'ilike', "%{$search}%");
                    });
            });
        }

        $perPage = $params['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    public function listPublic(User $user, array $params)
    {
        $query = $user->projects()
            ->whereNotNull('published_at')
            ->with('techStack')
            ->latest('published_at');

        if (isset($params['search']) && $params['search']) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhereHas('techStack', function ($q) use ($search) {
                        $q->where('name', 'ilike', "%{$search}%");
                    });
            });
        }

        $perPage = $params['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    public function getBySlug(User $user, string $slug): ?Project
    {
        return $user->projects()
            ->where('slug', $slug)
            ->whereNotNull('published_at')
            ->with('techStack')
            ->first();
    }

    public function getById($id): Project
    {
        return Project::with('techStack')->findOrFail($id);
    }

    public function createProject(User $user, array $data): Project
    {
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['user_id'] = $user->id;
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $data['thumbnail_path'] = $this->uploadFile($data['thumbnail'], 'projects');
        }

        $project = Project::create($data);

        if (isset($data['tech_stack'])) {
            $this->syncTechStack($project, $data['tech_stack']);
        }

        return $project;
    }

    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {

            if (isset($data['title']) && $data['title'] !== $project->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $project->id);
            }


            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {

                $data['thumbnail_path'] = $this->uploadFile(
                    $data['thumbnail'],
                    'projects',
                    $project->thumbnail_path
                );
            }

            $project->update($data);

            if (isset($data['tech_stack'])) {
                $this->syncTechStack($project, $data['tech_stack']);
            }

            return $project;
        });
    }

    private function generateUniqueSlug(string $title, ?string $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Project::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    private function syncTechStack(Project $project, array $tags): void
    {
        $ids = [];
        foreach ($tags as $tagName) {
            $cleanName = Str::title(trim($tagName));
            $skill = Skill::firstOrCreate(['name' => $cleanName]);
            $ids[] = $skill->id;
        }
        $project->techStack()->sync($ids);
    }
    public function deleteProject(Project $project): void
    {
        $this->deleteFile($project->thumbnail_path);
        $project->delete();
    }
}
