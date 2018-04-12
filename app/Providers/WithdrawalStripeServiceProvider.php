<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Withdrawals\Stripe;

class WithdrawalStripeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Stripe::class, function(){
            return new Stripe();
        });
    }
}
