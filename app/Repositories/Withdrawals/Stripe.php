<?php

namespace App\Repositories\Withdrawals;

use App\Contracts\Repositories\WithdrawFunds;
use Cartalyst\Stripe\Stripe as Merchant;
use App\Models\User;

use Illuminate\Support\Facades\Config;

/**
 * Class Stripe
 *
 * This class handles the withdrawal of funds process.
 *
 * @package App\Repositories\Withdrawals
 */
class Stripe implements WithdrawFunds
{

    protected $merchant;

    protected $user;

    public function __construct()
    {
        $this->merchant = new Merchant(Config::get('services.stripe.secret'), Config::get('services.stripe.api_version'));
    }


    public function withdraw($amount, $currency, $recipient)
    {
        $payoutData = array(
            'amount' => $amount,
            'currency' => $currency,
            'method' => 'instant'
        );

        $data = array($payoutData, array("stripe_account" => $recipient));

        $this->merchant->payouts()->create($data);
    }


    /**
     * Handles checking if the recipient of funds in our db
     *
     * @param $userId
     * @return bool
     */
    public function recipientExists($userId)
    {
        $user = User::find($userId);
        if($user->stripe_account_id){
            return $user->stripe_account_id;
        }else{
            return false;
        }
    }

}