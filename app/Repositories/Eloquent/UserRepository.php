<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function paginateOrdered(int $perPage = 15): LengthAwarePaginator
    {
        return User::orderBy('name')->paginate($perPage);
    }

    public function getAdmins(): Collection
    {
        return User::where('role', 'administrador')->get();
    }
}
