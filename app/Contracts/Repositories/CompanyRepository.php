<?php

namespace App\Contracts\Repositories;

interface CompanyRepository
{
    /**
     * Get the company with the given ID.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function find($id);

    /**
     * Create a new company with the given data.
     * @param  array  $data
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function create(array $data);

    /**
     * Updates a specific company with the given data
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Soft deletes a specific company
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

}
