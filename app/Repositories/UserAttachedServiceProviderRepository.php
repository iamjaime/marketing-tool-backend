<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserAttachedServiceProviderRepository as UserAttachedServiceProviderRepositoryContract;
use App\Models\UserAttachedServiceProvider;
use Illuminate\Support\Facades\Config;

class UserAttachedServiceProviderRepository implements UserAttachedServiceProviderRepositoryContract
{

    protected $userAttachedServiceProvider;

    /**
     * Handles the create new user attached service provider validation rules.
     * @var array
     */
    public $create_rules = [
        'provider_id' => 'required',
        'provider_account_id' => 'required|unique:user_attached_service_providers,provider_account_id',
        'traffic' => 'required'
    ];

    /**
     * Handles the update user attached service provider validation rules.
     * @var array
     */
    public $update_rules = [
        'traffic' => 'required',
        'provider_id' => 'required',
        'provider_account_id' => 'required|unique:user_attached_service_providers,provider_account_id'
    ];


    public function __construct(UserAttachedServiceProvider $userAttachedServiceProvider){
        $this->userAttachedServiceProvider = $userAttachedServiceProvider;
    }

    /**
     * Handles Finding a user attached service provider by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $userAttachedServiceProvider = $this->userAttachedServiceProvider->where('id', $id)->first();
        return $userAttachedServiceProvider;
    }


    /**
     * Handles creating new user attached service provider
     *
     * @param int $user_id
     * @param array $data
     * @return UserAttachedServiceProvider
     */
    public function create($user_id, array $data)
    {
        $this->userAttachedServiceProvider = new UserAttachedServiceProvider();
        $this->userAttachedServiceProvider->fill($data);
        $this->userAttachedServiceProvider->user_id = $user_id;
        $this->userAttachedServiceProvider->provider_id = $data['provider_id'];
        $this->userAttachedServiceProvider->net_worth = $data['traffic'] / Config::get('marketingtool.net_worth');
        $this->userAttachedServiceProvider->save();

        return $this->userAttachedServiceProvider;
    }

    /**
     * Handles updating user attached service provider
     *
     * @param int $user_id
     * @param array $data
     * @return UserAttachedServiceProvider
     */
    public function update($user_id, array $data)
    {
        $userAttachedServiceProvider = $this->userAttachedServiceProvider->where('user_id', $user_id)
            ->where('provider_id', '=', $data['provider_id'])
            ->where('provider_account_id', '=', $data['provider_account_id'])
            ->first();

        if(!$userAttachedServiceProvider){
            return false;
        }
        $userAttachedServiceProvider->fill($data);
        $this->userAttachedServiceProvider->net_worth = $data['traffic'] / Config::get('net_worth');
        $userAttachedServiceProvider->save();

        return $userAttachedServiceProvider;
    }

    /**
     * Handles Deleting user attached service provider
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $userAttachedServiceProvider = $this->userAttachedServiceProvider->where('id', $id)->first();
        if(!$userAttachedServiceProvider){
            return false;
        }
        $userAttachedServiceProvider->delete();
        return true;
    }

}