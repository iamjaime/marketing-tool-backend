<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserCompanyRepository as UserCompanyRepositoryContract;
use App\Models\UserCompany;


class UserCompanyRepository implements UserCompanyRepositoryContract
{

    protected $user_company;


    public function __construct(UserCompany $user_company){
        $this->user_company = $user_company;
    }

    /**
     * Handles finding a company attached to a user
     *
     * @param $user_id
     * @param $company_id
     * @return mixed
     */
    public function find($user_id, $company_id)
    {
        $user_company = $this->user_company->where('user_id', $user_id)->where('company_id', $company_id)->first();
        return $user_company;
    }


    /**
     * Handles attaching a company to a user
     *
     * @param $user_id
     * @param $company_id
     * @return UserCompany
     */
    public function attach($user_id, $company_id)
    {
        $user_company = new UserCompany();
        $user_company->user_id = $user_id;
        $user_company->company_id = $company_id;
        $user_company->save();

        return $user_company;
    }


    /**
     * Handles detaching a company from a user
     *
     * @param $user_id
     * @param $company_id
     * @return bool
     */
    public function detach($user_id, $company_id)
    {
        $user_company = $this->user_company->where('user_id', $user_id)->where('company_id', $company_id)->first();
        if(!$user_company){
            return false;
        }
        $user_company->delete();
        return true;
    }

}
