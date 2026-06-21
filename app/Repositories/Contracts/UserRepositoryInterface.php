<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function paginateOrdered(int $perPage = 15): LengthAwarePaginator;

    public function getAdmins(): Collection;
}
