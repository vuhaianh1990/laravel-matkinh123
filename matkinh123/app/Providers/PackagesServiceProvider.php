<?php

namespace Matkinh123\Providers;

use Illuminate\Support\ServiceProvider;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Barryvdh\Debugbar\Facade as Debugbar;

class PackagesServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register(DebugbarServiceProvider::class);

        $loader = AliasLoader::getInstance();
        $loader->alias('Debugbar', Debugbar::class);
    }
}
