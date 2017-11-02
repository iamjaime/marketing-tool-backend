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
     * Handles getting the service provider that this service is for.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serviceProvider()
    {
        return $this->belongsTo('App\Models\ServiceProvider', 'service_provider_id');
    }
}
