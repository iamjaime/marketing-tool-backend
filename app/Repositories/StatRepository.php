<?php

namespace App\Repositories;

use App\Models\Company;

class StatRepository
{

    protected $company;

    public function __construct(Company $company){
        $this->company = $company;
    }


}
