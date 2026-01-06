<?php

namespace App\Modules\Project\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail_url' => $this->thumbnail_path
                ? route('public.project.thumbnail', ['id' => $this->id])
                : null,
            'description' => $this->description,
            // 'content' excluded
            // 'demo_url' => $this->demo_url,  <-- User asked for demo_url
            // 'repository_url' => $this->repository_url, <-- User asked for repository_url
            'demo_url' => $this->demo_url,
            'repository_url' => $this->repository_url,
            'published_at' => is_string($this->published_at) ? substr($this->published_at, 0, 10) : $this->published_at?->format('Y-m-d'),
            'tech_stack' => $this->techStack->pluck('name'),
        ];
    }
}
