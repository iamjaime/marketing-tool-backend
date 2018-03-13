<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utils\Country;

class CountryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application.
     *
     * @return void
     */

    public function boot()
    {

    }

    /**
     * Register everything.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCountries();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerCountries()
    {
        $this->app->bind('countries', function($app)
        {
            return new Country();
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['countries'];
    }
}
