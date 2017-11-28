<?php

namespace App\Contracts\Repositories;

use App\Models\UserAttachedServiceProvider;

interface UserAttachedServiceProviderRepository
{
    /**
     * Handles Finding a user attached service provider by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id);

    /**
     * Handles creating new user attached service provider
     *
     * @param int $user_id
     * @param array $data
     * @return UserAttachedServiceProvider
     */
    public function create($user_id, array $data);

    /**
     * Handles updating user attached service provider
     *
     * @param int $user_id
     * @param array $data
     * @return UserAttachedServiceProvider
     */
    public function update($user_id, array $data);

    /**
     * Handles Deleting user attached service provider
     *
     * @param $id
     * @return bool
     */
    public function delete($id);
}