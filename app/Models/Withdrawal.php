<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'credits_withdrawn',
        'amount_paid_out',
        'transaction_fee',
        'type',
        'status',
    ];
}
