<?php

namespace App\Modules\Project\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
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
            'content' => $this->content,
            'demo_url' => $this->demo_url,
            'repository_url' => $this->repository_url,
            'published_at' => $this->published_at,
            'is_published' => !is_null($this->published_at),
            'tech_stack' => $this->techStack->pluck('name'),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
