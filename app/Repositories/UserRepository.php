<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository as UserRepositoryContract;
use App\Models\User;

class UserRepository implements UserRepositoryContract
{

    protected $user;

    /**
     * Handles the create new user validation rules.
     * @var array
     */
    public $create_rules = [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password'   => 'required',
        'provider' => 'required',
        'provider_id' => 'required'
    ];

    /**
     * Handles the update user validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required',
        'email' => 'sometimes|required|email|unique:users,email',
        'password'   => 'sometimes|required'
    ];


    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Handles Finding a user by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $user = $this->user->where('id', $id)->first();
        return $user;
    }

    /**
     * Handles Finding a user by email
     *
     * @param int $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        $user = $this->user->where('email', $email)->first();
        return $user;
    }


    /**
     * Handles creating new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $this->user = new User();
        $this->user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $this->user->save();

        return $this->user;
    }

    /**
     * Handles updating user
     *
     * @param array $data
     * @return User
     */
    public function update($id, array $data)
    {
        $user = $this->user->where('id', $id)->first();
        if(!$user){
            return false;
        }
        if(isset($data['password'])){
            $data['password'] = bcrypt($data['password']);
        }
        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * Handles Deleting User
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $user = $this->user->where('id', $id)->first();
        if(!$user){
            return false;
        }
        $user->delete();
        return true;
    }

}
