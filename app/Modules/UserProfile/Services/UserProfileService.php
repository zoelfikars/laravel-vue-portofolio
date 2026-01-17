<?php

namespace App\Modules\UserProfile\Services;

use App\Models\User;
use App\Modules\User\Models\Hobby;
use App\Modules\User\Models\Skill;
use App\Modules\UserProfile\Models\UserProfile;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Storage;
use Str;

class UserProfileService
{
    use FileUpload;
    public function getProfile(User $user)
    {
        $user->load(['hobbies', 'skills', 'experiences', 'projects', 'contacts']);
        $profile = $user->profile;
        if ($profile) {
            $profile->setRelation('user', $user);
        }
        return $profile;
    }
    public function saveProfile(User $user, array $data, $files = [])
    {
        if (isset($files['photo'])) {
            $data['photo_path'] = $this->uploadFile(
                $files['photo'],
                'profiles/photos',
                $user->profile->photo_path ?? null
            );
        }
        if (isset($files['cv'])) {
            $data['cv_path'] = $this->uploadFile(
                $files['cv'],
                'profiles/cvs',
                $user->profile->cv_path ?? null
            );
        }
        if (isset($data['is_active']) && $data['is_active']) {
            UserProfile::query()->update(['is_active' => false]);
        }

        if (isset($data['hobbies']) && is_array($data['hobbies'])) {
            $hobbyIds = [];
            foreach ($data['hobbies'] as $hobbyName) {
                $cleanName = Str::title(trim($hobbyName));
                $hobby = Hobby::firstOrCreate(['name' => $cleanName]);
                $hobbyIds[] = $hobby->id;
            }
            $user->hobbies()->sync($hobbyIds);
        }
        if (isset($data['skills']) && is_array($data['skills'])) {
            $skillIds = [];
            foreach ($data['skills'] as $skillName) {
                $cleanName = Str::title(trim($skillName));
                $skill = Skill::firstOrCreate(['name' => $cleanName]);
                $skillIds[] = $skill->id;
            }
            $user->skills()->sync($skillIds);
        }

        return UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }
    public function getHomeData()
    {
        return $this->getActiveProfile();
    }
    public function getActiveProfile()
    {
        return UserProfile::where('is_active', true)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email')
                        ->with([
                            'skills:id,name',
                            'hobbies:id,name',
                            'experiences',
                            'contacts',
                            'projects' => function ($q) {
                                $q->select([
                                    'id',
                                    'user_id',
                                    'title',
                                    'slug',
                                    'thumbnail_path',
                                    'description',
                                    'demo_url',
                                    'repository_url',
                                    'published_at'
                                ])
                                    ->whereNotNull('published_at')
                                    ->orderBy('published_at', 'desc')
                                    ->with('techStack:id,name');
                            }
                        ]);
                }
            ])
            ->first();
    }
    public function getFilePath($id, $type, $bypassActiveCheck = false)
    {
        $profile = UserProfile::findOrFail($id);
        $disk = config('filesystems.default');
        if ($type === 'cv') {
            if (!$bypassActiveCheck && !$profile->is_active) {
                return null;
            }
            $path = $profile->cv_path;
            if (!$path || !Storage::disk($disk)->exists($path)) {
                return null;
            }

            return $path;
        }
        if ($type === 'photo') {
            $path = $profile->photo_path;
            if (!$path || !Storage::disk($disk)->exists($path)) {
                return null;
            }
            return $path;
        }
        return null;
    }
    public function verifyCvPath($profile)
    {
        $disk = config('filesystems.default');
        $path = $profile->cv_path;
        if (!$path || !Storage::disk($disk)->exists($path)) {
            return null;
        }
        return $path;
    }
    public function getActiveUser(): ?User
    {
        $profile = UserProfile::where('is_active', true)->with('user')->first();
        return $profile ? $profile->user : null;
    }
}

