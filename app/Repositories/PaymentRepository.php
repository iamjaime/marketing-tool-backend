<?php

namespace App\Repositories;

use Cartalyst\Stripe\Stripe as Merchant;
use App\Repositories\UserRepository as User;
use App\Models\Payment;
use App\Models\PaymentMethod;

class PaymentRepository
{

    protected $merchant;
    protected $user;

    /**
     * Handles the create charge validation rules.
     * @var array
     */
    public $create_charge_rules = [
        'token' => 'required',
        'amount' => 'required'
    ];

    /**
     * Handles the update order validation rules.
     * @var array
     */
    public $update_rules = [
        'service_provider_id' => 'sometimes|required|exists:service_providers,id',
        'service_id' => 'sometimes|required|exists:services,id',
        'quantity' => 'sometimes|required',
        'is_complete' => 'sometimes|required'
    ];


    public function __construct(Merchant $merchant, User $user)
    {
        $this->merchant = $merchant;
        $this->user = $user;
    }


    /**
     * Handles creating a new customer
     *
     * @param $user_id
     * @return mixed
     */
    public function createCustomer($user_id)
    {
        $user = $this->user->find($user_id);

        if($user){
            if(!$user->stripe_customer_id){
                $customer = $this->merchant->customers()->create([
                    'email' => $user->email,
                ]);

                $user->stripe_customer_id = $customer['id'];
                $user->save();

                return $user;
            }else{
                return $user;
            }
        }

        return false;
    }


    /**
     * Handles getting a customer
     *
     * @param $user_id
     * @return mixed
     */
    public function getCustomer($user_id)
    {
        $user = $this->user->find($user_id);
        $customer = $this->merchant->customers()->find($user->stripe_customer_id);
        return $customer;
    }


    /**
     * Handles creating a new card
     *
     * @param $user_id
     * @param $token
     * @return object  The payment method
     */
    public function createCard($user_id, $token)
    {
        $user = $this->user->find($user_id);
        $card = $this->merchant->cards()->create($user->stripe_customer_id, $token);

        $payment_method = new PaymentMethod();
        $payment_method->user_id = $user_id;
        $payment_method->stripe_card_id = $card['id'];
        $payment_method->last4 = $card['last4'];
        $payment_method->exp_month = $card['exp_month'];
        $payment_method->exp_year = $card['exp_year'];
        $payment_method->country = $card['country'];
        $payment_method->brand = $card['brand'];
        $payment_method->save();

        return $payment_method;
    }


    /**
     * Handles charging a card
     *
     * @param $user_id
     * @param $amount
     * @return mixed
     */
    public function chargeCard($user_id, $token, $amount)
    {
        $user = $this->user->find($user_id);

        if(empty($user->payment_methods)){
            $card = $this->createCard($user_id, $token);

            $charge = $this->merchant->charges()->create([
                'customer' => $user->stripe_customer_id,
                'currency' => 'USD',
                'amount'   => $amount,
                'source' => $card['id']
            ]);
        }else{

            $card = $user->payment_methods[0];
            $charge = $this->merchant->charges()->create([
                'customer' => $user->stripe_customer_id,
                'currency' => 'USD',
                'amount'   => $amount,
                'source' => $card->stripe_card_id
            ]);
        }

        $card = PaymentMethod::where('stripe_card_id','=', $charge['source']['id'])->where('user_id', '=', $user_id)->first();

        $payment = new Payment();
        $payment->user_id = $user_id;
        $payment->charge_id = $charge['id'];
        $payment->amount = $charge['amount'];
        $payment->plan = $charge['description'];
        $payment->status = $charge['status'];
        $payment->meta_data = serialize($charge);
        $payment->card_id = $card->id;
        $payment->save();

        return $charge['id'];
    }


    /**
     * Handles subscribing user to a plan
     *
     * @param $user_id
     * @param $plan
     * @return mixed
     */
    public function subscribe($user_id, $plan, $token)
    {
        $user = $this->user->find($user_id);

        $subscription = $this->merchant->subscriptions()->create($user->stripe_customer_id, [
            'plan' => $plan,
            'source' => $token
        ]);

//        $card = PaymentMethod::where('stripe_card_id','=', $charge['source']['id'])->where('user_id', '=', $user_id)->first();
//
//        $payment = new Payment();
//        $payment->user_id = $user_id;
//        $payment->charge_id = $charge['id'];
//        $payment->amount = $charge['amount'];
//        $payment->plan = $charge['description'];
//        $payment->status = $charge['status'];
//        $payment->meta_data = serialize($charge);
//        $payment->card_id = $card->id;
//        $payment->save();

        return $subscription['id'];
    }

}
