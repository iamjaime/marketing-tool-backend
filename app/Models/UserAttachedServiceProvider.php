<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttachedServiceProvider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'traffic',
        'provider_account_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'provider_id',
    ];

    /**
     * Handles getting the social network service provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne('App\Models\ServiceProvider', 'id');
    }
}
