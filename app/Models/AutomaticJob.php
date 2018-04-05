<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomaticJob extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'days_remaining',
        'is_complete',
        'begin_date',
        'end_date'
    ];


    /**
     * Handles getting the subscription attached to this record
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription()
    {
        return $this->hasOne('App\Models\Payment', 'id', 'subscription_payment_id');
    }

    /**
     * Handles getting the order attached to this record
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('App\Models\Order', 'id', 'order_id');
    }
}
