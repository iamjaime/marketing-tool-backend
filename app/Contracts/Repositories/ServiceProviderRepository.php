<?php

namespace App\Contracts\Repositories;

interface ServiceProviderRepository
{
    /**
     * Get the service provider with the given ID.
     *
     * @param  int  $id
     * @return
     */
    public function find($id);

    /**
     * Create a new service provider with the given data.
     *
     * @param  array  $data
     * @return
     */
    public function create(array $data);

    /**
     * Updates a specific service provider with the given data
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Soft deletes a specific service provider
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

}
