<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeWithdrawalMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stripe_account_id',
        'method_type',
        'method_id',
        'brand',
        'country',
        'currency',
        'last4',
        'cvc_check',
        'exp_month',
        'exp_year',
        'is_instant_payout_available',
        'is_standard_payout_available',
    ];
}
