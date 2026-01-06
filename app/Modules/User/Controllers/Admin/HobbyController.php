<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\Hobby;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

use App\Modules\User\Services\HobbyService;

class HobbyController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(HobbyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $hobbies = $this->service->search($request->all());
        return $this->success($hobbies);
    }
}
