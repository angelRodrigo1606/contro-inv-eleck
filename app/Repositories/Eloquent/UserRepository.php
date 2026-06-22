<?php

namespace App\Repositories\Eloquent;

use App\Dtos\Data\UserData;
use App\Dtos\Input\StoreUserData;
use App\Dtos\Input\UpdateProfileData;
use App\Dtos\Input\UpdateUserData;
use App\Dtos\PaginatedData;
use App\Mappers\UserMapper;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function paginateOrdered(int $perPage = 15): PaginatedData
    {
        $paginator = User::orderBy('name')
            ->paginate($perPage);

        return PaginatedData::fromLengthAwarePaginator($paginator, [UserMapper::class, 'toData']);
    }

    public function findOrFail(int|string $id): UserData
    {
        return UserMapper::toData($this->doFindOrFail($id));
    }

    public function create(StoreUserData $data): UserData
    {
        $user = $this->doCreate([
            'name' => $data->name,
            'email' => $data->email,
            'role' => $data->role,
            'password' => $data->password,
        ]);

        return UserMapper::toData($user);
    }

    public function update(int|string $id, UpdateUserData $data): UserData
    {
        $payload = [
            'name' => $data->name,
            'email' => $data->email,
            'role' => $data->role,
        ];

        if ($data->password !== null) {
            $payload['password'] = $data->password;
        }

        $user = $this->doUpdate($id, $payload);

        return UserMapper::toData($user);
    }

    public function updateProfile(int|string $id, UpdateProfileData $data): UserData
    {
        $user = $this->doFindOrFail($id);
        $user->fill([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return UserMapper::toData($user);
    }

    public function delete(int|string $id): void
    {
        $this->doDelete($id);
    }

    public function getAdmins(): Collection
    {
        return UserMapper::toDataCollection(
            User::where('role', 'administrador')->get()
        );
    }

    public function verifyPassword(int|string $id, string $password): bool
    {
        $user = $this->doFindOrFail($id);

        return Hash::check($password, $user->password);
    }
}
