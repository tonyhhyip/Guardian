<?php

namespace Guardian\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Ramsey\Uuid\Uuid;

/**
 * ApiServiceProvider
 * This class extends Laravel's ServiceProvider class
 */
class ApiServiceProvider extends ServiceProvider {

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes.php';
        }
        Validator::extend('uuid', function ($attribute, $value, $parameter, $validator) {
            return Uuid::isValid($value);
        });
    }

}
