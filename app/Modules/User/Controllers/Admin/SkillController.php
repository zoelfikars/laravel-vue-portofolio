<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\Skill;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

use App\Modules\User\Services\SkillService;

class SkillController extends Controller
{
    use ApiResponse;

    protected $service;

    public function __construct(SkillService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $skills = $this->service->search($request->all());
        return $this->success($skills);
    }
}
