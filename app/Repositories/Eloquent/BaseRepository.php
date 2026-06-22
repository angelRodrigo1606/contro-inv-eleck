<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    public function __construct(protected Model $model) {}

    protected function doCreate(array $data): Model
    {
        return $this->model->create($data);
    }

    protected function doFind(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    protected function doFindOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    protected function doUpdate(int|string $id, array $data): Model
    {
        $model = $this->doFindOrFail($id);
        $model->update($data);

        return $model;
    }

    protected function doDelete(int|string $id): void
    {
        $this->doFindOrFail($id)->delete();
    }
}
