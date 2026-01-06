<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\Models\UserContact;
use App\Modules\User\Requests\UserContactRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Modules\User\Services\UserContactService;

class UserContactController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    protected $service;

    public function __construct(UserContactService $service)
    {
        $this->service = $service;
    }

    public function index(User $user)
    {
        $this->authorize('view', $user);
        return $this->success($this->service->list($user));
    }

    public function store(User $user, UserContactRequest $request)
    {
        $this->authorize('create', [UserContact::class, $user]);

        $contact = $this->service->create($user, $request->validated());

        return $this->success($contact, 'User contact added successfully', 201);
    }

    public function update(UserContact $contact, UserContactRequest $request)
    {
        $this->authorize('update', $contact);

        $contact = $this->service->update($contact, $request->validated());

        return $this->success($contact, 'User contact updated successfully');
    }

    public function destroy(UserContact $contact)
    {
        $this->authorize('delete', $contact);

        $this->service->delete($contact);

        return $this->success(null, 'User contact deleted successfully');
    }
}
