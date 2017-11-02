<?php

namespace App\Contracts\Repositories;

interface UserProvidingServiceRepository
{
    /**
     * Get the user providing service with the given ID.
     *
     * @param  int  $id
     * @return
     */
    public function find($id);

    /**
     * Create a new user providing service with the given data.
     *
     * @param  array  $data
     * @return
     */
    public function create(array $data);

    /**
     * Updates a specific user providing service with the given data
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Deletes a specific user providing service
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

}
