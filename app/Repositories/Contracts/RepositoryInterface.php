<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;
}
