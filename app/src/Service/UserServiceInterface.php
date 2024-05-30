<?php

namespace App\Service;

use App\Entity\User;


interface UserServiceInterface
{

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Delete entity.
     *
     * @param User $user
     */
    public function delete(User $user): void;
}
