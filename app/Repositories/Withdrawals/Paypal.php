<?php

namespace App\Repositories\Withdrawals;

use App\Contracts\Repositories\WithdrawFunds;


/**
 * Class Paypal
 *
 * This class handles the withdrawal of funds process.
 *
 * @package App\Repositories\Withdrawals
 */
class Paypal implements WithdrawFunds
{

    public function withdraw()
    {
        // TODO: Implement withdraw() method.
        return 'Paypal is working';
    }

}