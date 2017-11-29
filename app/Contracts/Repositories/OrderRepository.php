<?php

namespace App\Contracts\Repositories;

interface OrderRepository
{
    /**
     * Get the order with the given ID.
     *
     * @param  int  $id
     * @return
     */
    public function find($id);

    /**
     * Create a new order with the given data.
     * @param  integer $user_id
     * @param  array  $data
     * @return
     */
    public function create($user_id, array $data);

    /**
     * Updates a specific order with the given data
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Deletes a specific order
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

}
