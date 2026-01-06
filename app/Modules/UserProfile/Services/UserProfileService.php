<?php

namespace App\Modules\UserProfile\Services;

use App\Models\User;
use App\Modules\User\Models\Hobby;
use App\Modules\User\Models\Skill;
use App\Modules\UserProfile\Models\UserProfile;
use Illuminate\Support\Facades\Storage;
use Str;

class UserProfileService
{
    public function getProfile(User $user)
    {
        $user->load(['hobbies', 'skills']);
        return $user->profile;
    }

    public function saveProfile(User $user, array $data, $files = [])
    {
        if (isset($files['photo'])) {
            $data['photo_path'] = $this->handleFileUpload(
                $files['photo'],
                'profiles/photos',
                $user->profile->photo_path ?? null
            );
        }
        if (isset($files['cv'])) {
            $data['cv_path'] = $this->handleFileUpload(
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
                            'projects' => function ($q) { 
                                $q->whereNotNull('published_at')
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
        if ($type === 'cv') {
            if (!$bypassActiveCheck && !$profile->is_active) {
                return null;
            }

            return $profile->cv_path;
        }
        if ($type === 'photo') {
            return $profile->photo_path;
        }
        return null;
    }
    private function handleFileUpload($file, $path, $oldPath = null)
    {
        if ($oldPath && Storage::exists($oldPath)) {
            Storage::delete($oldPath);
        }
        return $file->store($path);
    }
}
