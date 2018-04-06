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
        'is_a_local_job',
        'radius',
        'fill_times',
        'language',
        'is_complete',
        'progress',
        'targret_url',
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

    /**
     * Handles getting the automatic job associated with this order
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function autoJob()
    {
        return $this->hasOne('App\Models\AutomaticJob', 'id', 'subscription_payment_id');
    }


    /**
     * Handles checking if within max distance
     *
     * @param $query
     * @param $lat
     * @param $lng
     * @param int $radius
     * @return mixed
     */
    public function scopeIsWithinMaxDistance($query, $lat, $lng, $radius = 5) {
        //$units = 6371; //km
        $units = 3959; //miles

        $haversine = "(".$units." * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(`latitude`)) 
                    * cos(radians(`longitude`) 
                    - radians(" . $lng . ")) 
                    + sin(radians(" . $lat . ")) 
                    * sin(radians(`latitude`))))";

        return $query->select('id', 'user_id')
            ->selectRaw("{$haversine} AS distance")
            ->whereRaw("{$haversine} < ?", [$radius]);
    }

}
