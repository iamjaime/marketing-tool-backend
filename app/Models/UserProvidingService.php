<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProvidingService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

    ];


    /**
     * Handles getting the user details that is providing the service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function serviceProvider()
    {
        return $this->hasOne('App\Models\User', 'user_id_providing_service');
    }


    /**
     * Handles getting the service buyer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function serviceBuyer()
    {
        return $this->hasOne('App\Models\User', 'user_id_of_buyer');
    }


    /**
     * Handles getting the order that the user is providing service for
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
