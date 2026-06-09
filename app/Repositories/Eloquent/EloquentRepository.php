<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * @return Builder<Model>
     */
    protected function newQuery(): Builder
    {
        return $this->modelClass()::query();
    }

    public function find(int $id): ?Model
    {
        return $this->newQuery()->find($id);
    }

    public function findOrFail(int $id): Model
    {
        return $this->newQuery()->findOrFail($id);
    }
}
