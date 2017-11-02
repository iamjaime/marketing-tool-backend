<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity',
        'is_complete',
        'progress',
        'total_cost',
        'currency'
    ];

    /**
     * Handles getting the service that the User has purchased.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->hasOne('App\Models\Service');
    }

    /**
     * Handles getting all of the users that are providing this service.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersProvidingService()
    {
        return $this->hasMany('App\Models\UserProvidingService');
    }
}
