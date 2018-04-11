<?php

namespace App\Contracts\Repositories;

interface WithdrawFunds
{
    /**
     * Handles withdrawing funds
     *
     * @return mixed
     */
    public function withdraw();

}
