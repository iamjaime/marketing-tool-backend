<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\PaymentRepository as Payment;
use App\Repositories\UserRepository as User;

class PaymentController extends Controller
{

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
                       
                        $payment = $this->payment->subscriptions( $this->userId(), $data['plan'], $data['token'] );
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





      

}
