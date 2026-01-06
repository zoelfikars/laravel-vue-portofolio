<?php

namespace App\Modules\Project\Models;

use App\Models\User;
use App\Modules\User\Models\Skill;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'thumbnail_path',
        'description',
        'content',
        'demo_url',
        'repository_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function techStack()
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
    }
}
