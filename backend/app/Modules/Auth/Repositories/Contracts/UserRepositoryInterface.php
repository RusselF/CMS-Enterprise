<?php

namespace App\Modules\Auth\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findByUuid(string $uuid): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;
}
