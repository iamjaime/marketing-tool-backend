<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\PaymentRepository as Payment;
use App\Repositories\UserRepository as User;
use App\Repositories\OrderRepository as Order;
use App\Repositories\Withdrawals\Paypal;
use App\Repositories\Withdrawals\Stripe;

class PaymentController extends Controller
{

    protected $payment;
    protected $user;
    protected $order;

    public function __construct(Payment $payment, User $user, Order $order)
    {
        $this->payment = $payment;
        $this->user = $user;
        $this->order = $order;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function chargeCard(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->payment->create_charge_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //If we pass validation lets create user and output success :)
        $stripeCustomer = $this->payment->createCustomer($this->userId());

        if($stripeCustomer->stripe_customer_id){
            $payment = $this->payment->chargeCard($this->userId(), $data['token'], $data['amount']);
            return response()->json([
                'success' => true,
                'data' => $payment
            ], 201);
        }


        return response()->json([
            'success' => false,
            'data' => ['error' => ['message' => 'there was an error charging the card']]
        ], 400);

    }



    /**
     * Handles subscribing user to a plan
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function plans(Request $request)
    {
        $data = $request->get('data'); 
          
        //validate....
        $rules = $this->payment->create_charge_rules_plan;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //If we pass validation lets create user and output success :)
        $stripeCustomer = $this->payment->createCustomer($this->userId());

        if($stripeCustomer->stripe_customer_id){

            $payment = $this->payment->subscribe($this->userId(), $data);

            //Everything was successful....Now add the credits....
            if($payment->status == "active"){
                $credits = $this->order->getCreditsNeeded($payment['meta_data']['quantity']);
                $this->user->addCredits($this->userId(), $credits);
            }

            return response()->json([
                'success' => true,
                'data' => $payment
            ], 201);
        }


        return response()->json([
            'success' => false,
            'data' => ['error' => ['message' => 'there was an error charging the card']]
        ], 400);
            
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stripeCustomerWebHook(Request $request)
    {
        $data = $request->get('data'); 
        $eventType = $request->get('type'); 
         

        //validate that our customer exists in the db....
         $rules = $this->payment->stripe_customer_webhook_rules;
         $validator = $this->validate($request, $rules);

         if(!empty($validator)){
             return response()->json([
                 'success' => false,
                 'data' => $validator
             ], 400);
         }

        $fees = $this->payment->attachProcessingFees($data, $eventType);

        return response()->json([
            'success' => true,
            'data' => $fees
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stripeWithdrawalWebHook(Request $request)
    {
        $data = $request->get('data');
        $eventType = $request->get('type');


        //validate that our customer exists in the db....
        $rules = $this->payment->stripe_withdrawal_webhook_rules;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        //lets update the payout record....
        $payout = $this->payment->updateStripePayoutRecord($data['object'], new Stripe());


        return response()->json([
            'success' => true,
            'data' => $payout
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stripeRecipient(Request $request)
    {
        $data = $request->get('data');

        $rules = $this->payment->create_withdrawal_stripe_recipient;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        $account = $this->payment->createStripeCustomAccount($this->userId(), $data);

        return response()->json([
            'success' => true,
            'data' => $account
        ], 200);
    }



     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscription(Request $request)
    {
        $data = $request->get('data');  
        $user = $this->payment->cancelSubscription($this->userId(),$data );

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Withdraw funds
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function withdrawStripe(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->payment->create_withdrawal_stripe;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }


        //check if user has sufficient funds....
        $amountPerCredit = config('marketingtool.net_worth');
        $amountInCredits = ($data['amount'] * ($amountPerCredit * 100)); //in pennies

        if(!$this->user->hasEnoughCredits($this->userId(), $amountInCredits)){
            return response()->json([
                'success' => false,
                'data' => ['error' => ['withdrawal' => 'You do not have enough credits to withdraw this amount.']]
            ], 400);
        }


        $withdrawal = $this->payment->withdrawWithStripe($this->userId(), $data, new Stripe());

        if(!$withdrawal) {
            return response()->json([
                'success' => false,
                'data' => ['error' => ['withdrawal' => 'there was an error withdrawing your funds. Please try again later, if the problem persists please contact customer support.']]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $withdrawal
        ], 200);
    }

    /**
     * Withdraw funds
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function withdrawPaypal(Request $request)
    {
        $data = $request->get('data');

        //validate....
        $rules = $this->payment->create_withdrawal_paypal;
        $validator = $this->validate($request, $rules);

        if(!empty($validator)){
            return response()->json([
                'success' => false,
                'data' => $validator
            ], 400);
        }

        $withdrawal = $this->payment->withdraw($this->userId(), $data, new Paypal());

        if(!$withdrawal) {
            return response()->json([
                'success' => false,
                'data' => ['error' => ['withdrawal' => 'there was an error withdrawing your funds. Please try again later, if the problem persists please contact customer support.']]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $withdrawal
        ], 200);
    }



      

}
