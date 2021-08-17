<?php

namespace Matkinh123\Providers;

use Illuminate\Support\ServiceProvider;
use Matkinh123\Providers\RouteServiceProvider;
use Matkinh123\Providers\PackagesServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load modules
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(PackagesServiceProvider::class);

        $this->loadViewsFrom(base_path('matkinh123/resources/views'), 'matkinh123');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
