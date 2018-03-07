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
        'city',
        'province',
        'postal_code',
        'country',
        'stripe_customer_id'
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
}
