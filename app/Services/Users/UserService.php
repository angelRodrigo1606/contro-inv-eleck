<?php

namespace App\Services\Users;

use App\Models\User;
use App\Services\Exceptions\SelfDeletionException;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }

    /**
     * @throws SelfDeletionException
     */
    public function delete(User $user, int $currentUserId): void
    {
        if ($user->id === $currentUserId) {
            throw new SelfDeletionException;
        }

        $user->delete();
    }
}
