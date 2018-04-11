<?php

namespace App\Repositories\Withdrawals;

use App\Contracts\Repositories\WithdrawFunds;


/**
 * Class Stripe
 *
 * This class handles the withdrawal of funds process.
 *
 * @package App\Repositories\Withdrawals
 */
class Stripe implements WithdrawFunds
{

    public function withdraw()
    {
        // TODO: Implement withdraw() method.
        return 'Stripe is working';
    }

}