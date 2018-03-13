<?php

namespace App\Contracts\Repositories;

interface UserCompanyRepository
{
    /**
     * Handles Finding a company attached to a user
     *
     * @param int $user_id
     * @param int $company_id
     * @return mixed
     */
    public function find($user_id, $company_id);

    /**
     * Handles attaching a company to a user
     *
     * @param $user_id
     * @param $company_id
     * @return UserCompany
     */
    public function attach($user_id, $company_id);

    /**
     * Handles detaching a company from a user
     *
     * @param $user_id
     * @param $company_id
     * @return bool
     */
    public function detach($user_id, $company_id);

}