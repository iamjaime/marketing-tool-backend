<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository as UserRepositoryContract;
use App\Models\User;
use Carbon\Carbon;


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
        'password'   => 'required'
    ];

    /**
     * Handles the update user validation rules.
     * @var array
     */
    public $update_rules = [
        'name' => 'sometimes|required',
        'email' => 'sometimes|required|email|unique:users,email',
        'password'   => 'sometimes|required',
        'primary_language_id'   => 'sometimes|required|exists:languages,id',
        'city' => 'sometimes|required',
        'province' => 'sometimes|required',
        'postal_code' => 'sometimes|required',
        'country' => 'sometimes|required'
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
        $user = $this->user->where('id', $id)->with(['primaryLanguage', 'attachedNetworks.provider', 'paymentMethods'])->first();
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
        $user = $this->user->where('email', $email)->with(['primaryLanguage', 'attachedNetworks.provider', 'paymentMethods'])->first();
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

        $data['primary_language_id'] = 1;
        $this->user->primary_language_id = $data['primary_language_id'];
        $data['password'] = bcrypt($data['password']);

        if(isset($data['dob'])){
            $data['dob'] = Carbon::createFromFormat('m/d/Y', $data['dob'])->format('Y-m-d');
        }

        $this->user->fill($data);
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
        $user = $this->user->where('id', $id)->with(['primaryLanguage', 'attachedNetworks.provider'])->first();
        if(!$user){
            return false;
        }
        if(isset($data['password'])){
            $data['password'] = bcrypt($data['password']);
        }

        if(isset($data['dob'])){
            $data['dob'] = Carbon::createFromFormat('m/d/Y', $data['dob'])->format('Y-m-d');
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * Handles add user credits
     *
     * @param $user_id
     * @param $credits
     * @return mixed
     */
    public function addCredits($user_id, $credits)
    {
        $user = $this->user->where('id', '=', $user_id)->first();
        $user->credits = $user->credits + $credits;
        $user->save();

        return $user;
    }

    /**
     * Handles deduct user credits
     *
     * @param $user_id
     * @param $credits
     * @return mixed
     */
    public function deductCredits($user_id, $credits)
    {
        $user = $this->user->where('id', '=', $user_id)->first();
        $user->credits = $user->credits - $credits;
        $user->save();

        return $user;
    }


    /**
     * Handles checking if the user has enough credits
     *
     * @param $user_id
     * @param $credits
     * @return bool
     */
    public function hasEnoughCredits($user_id, $credits)
    {
        $user = $this->user->where('id','=', $user_id)->first();
        if($user->credits < $credits){
            return false;
        }else{
            return true;
        }
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
