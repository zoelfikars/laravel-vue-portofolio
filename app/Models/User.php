<?php

namespace App\Models;

use App\Modules\Project\Models\Project;
use App\Modules\User\Models\Experience;
use App\Modules\User\Models\Hobby;
use App\Modules\User\Models\Skill;
use App\Modules\UserProfile\Models\UserProfile;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function hobbies()
    {
        return $this->belongsToMany(Hobby::class, 'hobby_user');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
