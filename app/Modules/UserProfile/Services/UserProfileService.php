<?php

namespace App\Modules\UserProfile\Services;

use App\Models\User;
use App\Modules\UserProfile\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    public function getProfile(User $user)
    {
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
        return UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }

    public function getActiveProfile()
    {
        return UserProfile::with('user:id,name,email')
            ->where('is_active', true)
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
