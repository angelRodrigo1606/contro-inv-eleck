<?php

namespace App\Services\Users;

use App\Dtos\Data\UserData;
use App\Dtos\Input\StoreUserData;
use App\Dtos\Input\UpdateUserData;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Exceptions\SelfDeletionException;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function create(StoreUserData $data): UserData
    {
        return $this->userRepository->create($this->withHashedPassword($data));
    }

    public function update(int|string $id, UpdateUserData $data): UserData
    {
        return $this->userRepository->update($id, $this->withHashedPassword($data));
    }

    public function updatePassword(int|string $id, string $password): UserData
    {
        $user = $this->userRepository->findOrFail($id);

        return $this->userRepository->update($id, new UpdateUserData(
            name: $user->name,
            email: $user->email,
            role: $user->role,
            password: Hash::make($password),
        ));
    }

    public function createRegisteredUser(string $name, string $email, string $password): UserData
    {
        return $this->create(new StoreUserData(
            name: $name,
            email: $email,
            role: 'empleado',
            password: $password,
        ));
    }

    /**
     * @throws SelfDeletionException
     */
    public function delete(int|string $id, int $currentUserId): void
    {
        if ((int) $id === $currentUserId) {
            throw new SelfDeletionException;
        }

        $this->userRepository->delete($id);
    }

    private function withHashedPassword(StoreUserData|UpdateUserData $data): StoreUserData|UpdateUserData
    {
        if ($data instanceof UpdateUserData && $data->password === null) {
            return $data;
        }

        $hashed = Hash::make($data->password);

        return $data instanceof StoreUserData
            ? new StoreUserData($data->name, $data->email, $data->role, $hashed)
            : new UpdateUserData($data->name, $data->email, $data->role, $hashed);
    }
}
