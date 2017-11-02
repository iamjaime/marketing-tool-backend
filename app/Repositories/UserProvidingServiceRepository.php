<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserProvidingServiceRepository as UserProvidingServiceRepositoryContract;
use App\Models\UserProvidingService;

class UserProvidingServiceRepository implements UserProvidingServiceRepositoryContract
{

    protected $userProvidingService;

    /**
     * Handles the create new user providing service validation rules.
     * @var array
     */
    public $create_rules = [
        'name' => 'required'
    ];

    /**
     * Handles the update user providing service validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required'
    ];


    public function __construct(UserProvidingService $userProvidingService){
        $this->userProvidingService = $userProvidingService;
    }

    /**
     * Handles Finding a user providing service by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $userProvidingService = $this->userProvidingService->where('id', $id)->first();
        return $userProvidingService;
    }


    /**
     * Handles creating new user providing service
     *
     * @param array $data
     * @return UserProvidingService
     */
    public function create(array $data)
    {
        $this->userProvidingService = new UserProvidingService();
        $this->userProvidingService->fill($data);
        $this->userProvidingService->save();

        return $this->userProvidingService;
    }

    /**
     * Handles updating user providing service
     *
     * @param array $data
     * @return UserProvidingService
     */
    public function update($id, array $data)
    {
        $userProvidingService = $this->userProvidingService->where('id', $id)->first();
        if(!$userProvidingService){
            return false;
        }
        $userProvidingService->fill($data);
        $userProvidingService->save();

        return $userProvidingService;
    }

    /**
     * Handles Deleting user providing service
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $userProvidingService = $this->userProvidingService->where('id', $id)->first();
        if(!$userProvidingService){
            return false;
        }
        $userProvidingService->delete();
        return true;
    }

}
