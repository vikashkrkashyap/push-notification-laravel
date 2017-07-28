<?php

namespace PushNotification\Provider;

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

        if (! $this->app->routesAreCached()) {
            require __DIR__ . '../routes/routes.php';
        }

        $this->publishes([
            __DIR__ . '/config/pushNotification.php' => config_path('pushNotification.php'),
        ]);

        // require __DIR__ . '/../../vendor/autoload.php';
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
