<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'latitude',
        'longitude',
        'company_name',
        'company_email',
        'company_phone',
        'company_logo',
        'company_description',
        'company_address',
        'company_city',
        'company_province',
        'company_postal_code',
        'company_country',
        'url',
        'interested_in',
        'interested_in_service_providers',
        'budget_for_marketing',
        'budget_for_marketing_frequency',
        'engagement_bonus',
        'engagement_bonus_in_smi_credits_per_sale',
        'when_do_you_want_to_begin',
    ];

    /**
     * Handles getting the company's primary language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryLanguage()
    {
        return $this->hasOne('App\Models\Language', 'id', 'primary_language_id');
    }
}
