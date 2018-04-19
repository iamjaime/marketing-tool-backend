<?php

namespace App\Repositories\Withdrawals;

use App\Contracts\Repositories\WithdrawFunds;
use App\Utils\Stripe\StripePackage as Merchant;
use App\Models\User;
use App\Models\StripeWithdrawalMethod;
use App\Models\StripeWithdrawal;
use Carbon\Carbon;
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
        //first transfer funds from my stripe account to the recipients account....
        $transfer = $this->transferFunds($amount, $currency, $recipient);
        if($transfer['id']){
            //now that the funds have been transferred to the recipient....
            //lets do the payout....
            return $this->payoutFunds($amount, $currency, $recipient);
        }
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


    /**
     * Handles transferring the funds from stripe account to stripe account.
     *
     * @param $amount
     * @param $currency
     * @param $recipient
     * @return mixed
     */
    public function transferFunds($amount, $currency, $recipient)
    {
        return $transfer = $this->merchant->transfers()->create([
            'amount' => $amount,
            'currency' => $currency,
            'destination' => $recipient,
        ]);
    }


    /**
     * Handles withdrawing the funds from the stripe account of the end user
     * to their bank account or debit card
     *
     * @param $amount
     * @param $currency
     * @param $recipient
     * @return mixed
     */
    public function payoutFunds($amount, $currency, $recipient)
    {
        $stripeWithdrawalMethods = StripeWithdrawalMethod::where('stripe_account_id', '=', $recipient)->first();
        $payoutType = 'standard';
        if($stripeWithdrawalMethods->is_instant_payout_available){
            $payoutType = 'instant';
        }

        //Set the Stripe Header Account Id....
        $this->merchant->payouts()->config->setAccountId($recipient);

        $payout = $this->merchant->payouts()->create(array(
            "amount" => $amount,
            "currency" => $currency,
            "method" => $payoutType
        ));

        $user = User::where('stripe_account_id', '=', $recipient)->first();

        $withdrawal = new StripeWithdrawal();
        $withdrawal->user_id = $user->id;
        $withdrawal->payout_id = $payout['id'];
        $withdrawal->credits_withdrawn = $payout['amount'];
        $withdrawal->amount_paid_out = $payout['amount'];
        $withdrawal->amount_paid_out = $payout['amount'];
        $withdrawal->arrival_date = Carbon::createFromTimestamp($payout['arrival_date'])->format('Y-m-d H:i:s');
        $withdrawal->automatic = $payout['automatic'];
        $withdrawal->balance_transaction = $payout['balance_transaction'];
        $withdrawal->currency = $payout['currency'];
        $withdrawal->description = $payout['description'];
        $withdrawal->destination = $payout['destination'];
        $withdrawal->failure_balance_transaction = $payout['failure_balance_transaction'];
        $withdrawal->failure_code = $payout['failure_code'];
        $withdrawal->failure_message = $payout['failure_message'];
        $withdrawal->live_mode = $payout['livemode'];
        $withdrawal->method = $payout['method'];
        $withdrawal->source_type = $payout['source_type'];
        $withdrawal->statement_descriptor = $payout['statement_descriptor'];
        $withdrawal->status = $payout['status'];
        $withdrawal->type = $payout['type'];
        $withdrawal->save();

        return $withdrawal;
    }


    /**
     * Handles updating the payout record from the webhook
     *
     * @param $payoutId
     * @param $data
     * @return mixed
     */
    public function updatePayoutRecord($payoutId, $data)
    {
        $withdrawal = StripeWithdrawal::where('id', '=', $payoutId)->first();
        //Failure.....
        $withdrawal->failure_balance_transaction = $data['failure_balance_transaction'];
        $withdrawal->failure_code = $data['failure_code'];
        $withdrawal->failure_message = $data['failure_message'];
        $withdrawal->status = $data['status'];
        $withdrawal->save();

        return $withdrawal;
    }

}