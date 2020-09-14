<?php

namespace Seobrain\Laraparser;

use Illuminate\Support\ServiceProvider;

class LaraparserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laraparser.php', 'laraparser');
        $this->app->singleton(Laraparser::class, function(){ return new Laraparser(); });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laraparser.php' => config_path('laraparser.php')
        ]);
    }
}
