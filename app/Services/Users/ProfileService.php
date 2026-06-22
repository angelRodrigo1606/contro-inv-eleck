<?php

namespace App\Services\Users;

use App\Dtos\Data\UserData;
use App\Dtos\Input\UpdateProfileData;
use App\Repositories\Contracts\UserRepositoryInterface;

class ProfileService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function update(int $userId, UpdateProfileData $data): UserData
    {
        return $this->userRepository->updateProfile($userId, $data);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function delete(int $userId, string $password): void
    {
        if (! $this->userRepository->verifyPassword($userId, $password)) {
            throw new \InvalidArgumentException('La contraseña proporcionada no es correcta.');
        }

        $this->userRepository->delete($userId);
    }
}
