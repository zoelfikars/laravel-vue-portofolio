<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\Models\UserContact;

class UserContactService
{
    public function list(User $user)
    {
        return $user->contacts;
    }

    public function create(User $user, array $data): UserContact
    {
        return $user->contacts()->create($data);
    }

    public function update(UserContact $contact, array $data): UserContact
    {
        $contact->update($data);
        return $contact;
    }

    public function delete(UserContact $contact): void
    {
        $contact->delete();
    }
}
