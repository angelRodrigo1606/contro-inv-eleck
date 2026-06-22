<?php

namespace App\Mappers;

use App\Dtos\Data\UserData;
use App\Models\User;
use Illuminate\Support\Collection;

class UserMapper
{
    public static function toData(User $user): UserData
    {
        return new UserData(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $user->role,
            emailVerifiedAt: $user->email_verified_at?->toDateTimeImmutable(),
            createdAt: $user->created_at?->toDateTimeImmutable(),
            updatedAt: $user->updated_at?->toDateTimeImmutable(),
        );
    }

    /**
     * @param  Collection<int, User>  $users
     * @return Collection<int, UserData>
     */
    public static function toDataCollection(Collection $users): Collection
    {
        return $users->map(fn (User $user) => self::toData($user));
    }
}
