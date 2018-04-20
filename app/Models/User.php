<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'address',
        'city',
        'credits',
        'province',
        'postal_code',
        'country',
        'stripe_customer_id',
        'referred_by',
        'heard_about_smi',
        'interested_in',
        'interested_in_working_with_smi',
        'interested_in_investing_in_smi',
        'interested_in_using_smi_for_publicity',
        'earning_goal_amount',
        'earning_currency',
        'earning_frequency',
        'daily_working_frequency',
        'possible_investment_amount',
        'investment_currency',
        'publicity_amount_spent',
        'publicity_currency',
        'publicity_frequency',
        'latitude',
        'longitude',
        'dob',
        'locale',
        'gender',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Handles getting the user's primary language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryLanguage()
    {
        return $this->hasOne('App\Models\Language', 'id', 'primary_language_id');
    }

    /**
     * Handles getting the logged in user's attached social media networks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachedNetworks()
    {
        return $this->hasMany('App\Models\UserAttachedServiceProvider', 'user_id');
    }

    /**
     * Handles getting the orders that the logged in user has purchased.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchasedOrders()
    {
        return $this->hasMany('App\Models\Order', 'user_id');
    }

    /**
     * Handles getting the user's attached payment methods
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentMethods()
    {
        return $this->hasMany('App\Models\PaymentMethod', 'user_id');
    }

    /**
     * Handles getting the companies that belong to the logged in user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function companies()
    {
        return $this->hasMany('App\Models\Company', 'user_id');
    }


    /**
     * Handles getting the user's subscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany('App\Models\Payment', 'user_id');
    }

    /**
     * Handles getting the User's Stripe withdrawal methods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stripeWithdrawalMethods()
    {
        return $this->hasMany('App\Models\StripeWithdrawalMethod', 'user_id');
    }

    /**
     * Handles getting the user's withdrawal history
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stripeWithdrawals()
    {
        return $this->hasMany('App\Models\StripeWithdrawal', 'user_id');
    }

}
