<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\Hobby;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class HobbyController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $search = $request->input('search');

        $hobbies = Hobby::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'name']);

        return $this->success($hobbies);
    }
}
