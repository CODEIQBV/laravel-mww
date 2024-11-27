<?php

namespace YourNamespace\MyOnlineStore;

use Illuminate\Support\ServiceProvider;

class MyOnlineStoreServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/myonlinestore.php' => config_path('myonlinestore.php'),
        ], 'myonlinestore-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/myonlinestore.php', 'myonlinestore'
        );

        $this->app->singleton('myonlinestore', function ($app) {
            return new MyOnlineStore(config('myonlinestore'));
        });
    }
} 