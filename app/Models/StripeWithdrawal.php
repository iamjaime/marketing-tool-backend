<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeWithdrawal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payout_id',
        'credits_withdrawn',
        'amount_paid_out',
        'arrival_date',
        'automatic',
        'balance_transaction',
        'currency',
        'description',
        'destination',
        'failure_balance_transaction',
        'failure_code',
        'failure_message',
        'live_mode',
        'method',
        'source_type',
        'statement_descriptor',
        'status',
        'type',
    ];

    /**
     * Handles returning the payout method attached to this withdrawal record
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payoutMethod()
    {
        return $this->hasOne('App\Models\StripeWithdrawalMethod', 'method_id', 'destination');
    }
}
