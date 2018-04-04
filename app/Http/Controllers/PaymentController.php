<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\PaymentRepository as Payment;
use App\Repositories\UserRepository as User;
use App\Repositories\OrderRepository as Order;

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
                $this->user->addCredits($this->userId(), $payment['meta_data']['quantity']);
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
    public function webHook(Request $request)
    {
        $data = $request->get('data'); 
        $eventType = $request->get('type'); 
         

        //validate that our customer exists in the db....
         $rules = $this->payment->webhook_rules;
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
    public function cancelSubscription(Request $request)
    {
        $data = $request->get('data');  
        $user = $this->payment->cancelSubscription($this->userId(),$data );

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }





      

}
