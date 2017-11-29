<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'service_provider_id'
    ];

    /**
     * Handles getting the service provider that this service is for.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serviceProvider()
    {
        return $this->belongsTo('App\Models\ServiceProvider', 'service_provider_id');
    }
}
