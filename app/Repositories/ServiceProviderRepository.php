<?php

namespace App\Repositories;

use App\Contracts\Repositories\ServiceProviderRepository as ServiceProviderRepositoryContract;
use App\Models\ServiceProvider;

class ServiceProviderRepository implements ServiceProviderRepositoryContract
{

    protected $serviceProvider;

    /**
     * Handles the create new service provider validation rules.
     * @var array
     */
    public $create_rules = [
        'name' => 'required'
    ];

    /**
     * Handles the update service provider validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required'
    ];


    public function __construct(ServiceProvider $serviceProvider){
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * Handles Finding a service provider by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $serviceProvider = $this->serviceProvider->where('id', $id)->first();
        return $serviceProvider;
    }


    /**
     * Handles creating new service provider
     *
     * @param array $data
     * @return ServiceProvider
     */
    public function create(array $data)
    {
        $this->serviceProvider = new ServiceProvider();
        $this->serviceProvider->fill($data);
        $this->serviceProvider->save();

        return $this->serviceProvider;
    }

    /**
     * Handles updating service provider
     *
     * @param array $data
     * @return ServiceProvider
     */
    public function update($id, array $data)
    {
        $serviceProvider = $this->serviceProvider->where('id', $id)->first();
        if(!$serviceProvider){
            return false;
        }
        $serviceProvider->fill($data);
        $serviceProvider->save();

        return $serviceProvider;
    }

    /**
     * Handles Deleting service provider
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $serviceProvider = $this->serviceProvider->where('id', $id)->first();
        if(!$serviceProvider){
            return false;
        }
        $serviceProvider->delete();
        return true;
    }

}
