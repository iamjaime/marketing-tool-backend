<?php

namespace App\Repositories\Withdrawals;

use App\Contracts\Repositories\WithdrawFunds;
use Cartalyst\Stripe\Stripe as Merchant;
use App\Models\User;
use App\Models\StripeWithdrawalMethod;

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
        $amount = $amount / 100; //take the pennies and convert them to dollars.....
        $stripeWithdrawalMethods = StripeWithdrawalMethod::where('stripe_account_id', '=', $recipient)->first();
        $payoutType = 'standard';
        if($stripeWithdrawalMethods->is_instant_payout_available){
            $payoutType = 'instant';
        }

        $this->merchant->payouts()->create(array(
            "amount" => $amount,
            "currency" => $currency,
            "method" => $payoutType
        ),
            array("stripe_account" => $recipient));
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