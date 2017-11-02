<?php

namespace App\Repositories;

use App\Contracts\Repositories\ServiceRepository as ServiceRepositoryContract;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryContract
{

    protected $service;

    /**
     * Handles the create new service validation rules.
     * @var array
     */
    public $create_rules = [
        'name' => 'required'
    ];

    /**
     * Handles the update service validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required'
    ];


    public function __construct(Service $service){
        $this->service = $service;
    }

    /**
     * Handles Finding a service by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $service = $this->service->where('id', $id)->first();
        return $service;
    }


    /**
     * Handles creating new service
     *
     * @param array $data
     * @return Service
     */
    public function create(array $data)
    {
        $this->service = new Service();
        $this->service->fill($data);
        $this->service->save();

        return $this->service;
    }

    /**
     * Handles updating service
     *
     * @param array $data
     * @return Service
     */
    public function update($id, array $data)
    {
        $service = $this->service->where('id', $id)->first();
        if(!$service){
            return false;
        }
        $service->fill($data);
        $service->save();

        return $service;
    }

    /**
     * Handles Deleting service
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $service = $this->service->where('id', $id)->first();
        if(!$service){
            return false;
        }
        $service->delete();
        return true;
    }

}
