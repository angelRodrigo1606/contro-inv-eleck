<?php

namespace App\Services\Users;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function update(User $user, array $data): User
    {
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        return $this->userRepository->save($user);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function delete(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw new \InvalidArgumentException('La contraseña proporcionada no es correcta.');
        }

        $this->userRepository->delete($user);
    }
}
