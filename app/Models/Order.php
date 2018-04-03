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
        'fill_times',
        'language',
        'is_complete',
        'progress',
        'url',
        'image_url',
        'title',
        'description',
        'latitude',
        'longitude'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'service_id',
        'service_provider_id'
    ];

    /**
     * Handles Getting the Buyer details
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function buyer()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


    /**
     * Handles getting the social media network (example : Facebook, Instagram etc.)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne('App\Models\ServiceProvider', 'id', 'service_provider_id');
    }

    /**
     * Handles getting the service that the User has purchased.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->hasOne('App\Models\Service', 'id', 'service_id');
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
