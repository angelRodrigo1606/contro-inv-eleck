<?php

namespace App\Services\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Exceptions\SelfDeletionException;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->create($data);
    }

    public function update(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepository->update($user, $data);
    }

    /**
     * @throws SelfDeletionException
     */
    public function delete(User $user, int $currentUserId): void
    {
        if ($user->id === $currentUserId) {
            throw new SelfDeletionException;
        }

        $this->userRepository->delete($user);
    }
}
