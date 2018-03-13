<?php

namespace App\Repositories;

use App\Contracts\Repositories\CompanyRepository as CompanyRepositoryContract;
use App\Models\Company;
use App\Models\UserCompany;

class CompanyRepository implements CompanyRepositoryContract
{

    protected $company;
    protected $user_company;

    /**
     * Handles the create new company validation rules.
     * @var array
     */
    public $create_rules = [
        'company_name' => 'required',
        'company_email' => 'required',
        'company_phone' => 'required',
//        'company_logo' => 'required',
        'company_description' => 'required',
        'company_address' => 'required',
        'company_city' => 'required',
        'company_province' => 'required',
        'company_postal_code' => 'required',
        'company_country' => 'required',
        'url' => 'required',
        'interested_in' => 'required',
        'interested_in_service_providers' => 'required',
        'budget_for_marketing' => 'required',
        'budget_for_marketing_frequency' => 'required',
        'engagement_bonus' => 'required|boolean',
        'engagement_bonus_in_smi_credits_per_sale' => 'required_if:engagement_bonus,true',
        'when_do_you_want_to_begin' => 'required',
    ];

    /**
     * Handles the update company validation rules.
     * @var array
     */
    public $update_rules = [
        'company_name' => 'sometimes|required',
        'company_email' => 'sometimes|required',
        'company_phone' => 'sometimes|required',
        'company_logo' => 'sometimes|required',
        'company_description' => 'sometimes|required',
        'company_address' => 'sometimes|required',
        'company_city' => 'sometimes|required',
        'company_province' => 'sometimes|required',
        'company_postal_code' => 'sometimes|required',
        'company_country' => 'sometimes|required',
        'url' => 'sometimes|required',
        'interested_in' => 'sometimes|required',
        'interested_in_service_providers' => 'sometimes|required',
        'budget_for_marketing' => 'sometimes|required',
        'budget_for_marketing_frequency' => 'sometimes|required',
        'engagement_bonus' => 'sometimes|required',
        'engagement_bonus_in_smi_credits' => 'sometimes|required',
        'when_do_you_want_to_begin' => 'sometimes|required',
    ];


    public function __construct(Company $company, UserCompany $userCompany){
        $this->company = $company;
        $this->user_company = $userCompany;
    }

    /**
     * Handles Finding a company by id
     *
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        $company = $this->company->where('id', $id)->with(['primaryLanguage'])->first();
        return $company;
    }



    /**
     * Handles Finding a company by email
     *
     * @param int $email
     * @return mixed
     */
    public function findByEmail($email)
    {
        $company = $this->company->where('company_email', $email)->with(['primaryLanguage'])->first();
        return $company;
    }


    /**
     * Handles creating new company
     *
     * @param array $data
     * @return Company
     */
    public function create(array $data)
    {
        $company = new Company();
        $company->primary_language_id = 1;
        $company->fill($data);
        $company->save();

        return $company;
    }

    /**
     * Handles updating company
     *
     * @param array $data
     * @return Company
     */
    public function update($id, array $data)
    {
        $company = $this->company->where('id', $id)->with(['primaryLanguage'])->first();
        if(!$company){
            return false;
        }
        $company->fill($data);
        $company->save();

        return $company;
    }



    /**
     * Handles Deleting Company
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $company = $this->company->where('id', $id)->first();
        if(!$company){
            return false;
        }
        $company->delete();
        return true;
    }


    /**
     * Handles activating a company
     *
     * @param $company_id
     * @return bool|mixed
     */
    public function activate($company_id)
    {
        $company = $this->find($company_id);
        if($company){
            $company->is_active = true;
            $company->save();
            return $company;
        }
        return false;
    }

    /**
     * Handles deactivating a company
     *
     * @param $company_id
     * @return bool|mixed
     */
    public function deactivate($company_id)
    {
        $company = $this->find($company_id);
        if($company){
            $company->is_active = false;
            $company->save();
            return $company;
        }
        return false;
    }
}
