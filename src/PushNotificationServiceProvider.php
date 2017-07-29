<?php

namespace PushNotification;

use Illuminate\Support\ServiceProvider;
use PushNotification\Contracts\PushContract;
use PushNotification\Controllers\PushNotificationController;

class PushNotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PushContract::class, PushNotificationController::class);

        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $configPath = __DIR__ . '/config/pushNotification.php';
        $this->publishes([
            $configPath => config_path('pushNotification.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
