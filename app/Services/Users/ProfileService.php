<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function delete(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw new \InvalidArgumentException('La contraseña proporcionada no es correcta.');
        }

        $user->delete();
    }
}
