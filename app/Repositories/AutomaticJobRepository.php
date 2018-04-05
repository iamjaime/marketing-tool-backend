<?php

namespace App\Repositories;

use App\Models\AutomaticJob;

class AutomaticJobRepository
{


    public function __construct()
    {

    }


    /**
     * Handles creating the new automatic job
     *
     * @param $data
     */
    public function create($data)
    {
        $job = new AutomaticJob();
        $job->order_id = $data['order_id'];
        $job->subscription_payment_id = $data['subscription_payment_id'];
        $job->fill($data);
        $job->save();
    }

}