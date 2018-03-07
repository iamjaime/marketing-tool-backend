<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stripe_card_id',
        'last4',
        'exp_month',
        'exp_year',
        'country',
        'brand',
        'stripe_charge_token',
    ];
}
