<?php

namespace App\Modules\UserProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\UserProfile\Services\UserProfileService;
use App\Modules\UserProfile\Resources\UserProfileResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicProfileController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }

    public function getActive()
    {
        $profile = $this->service->getActiveProfile();

        if (!$profile) {
            return $this->error('Profil aktif tidak ditemukan', 404);
        }

        return $this->success(new UserProfileResource($profile), 'Berhasil mengambil data profil aktif');
    }

    public function streamPhoto($id)
    {
        $path = $this->service->getFilePath($id, 'photo');

        if (!$path || !Storage::exists($path)) {
            abort(404);
        }

        return Storage::response($path);
    }

    public function downloadCv($id)
    {
        $path = $this->service->getFilePath($id, 'cv', false);

        if (!$path || !Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }
}
