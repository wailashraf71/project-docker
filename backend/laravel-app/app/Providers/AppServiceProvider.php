<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        static $registered = false;
        if ($registered) {
            return;
        }
        $registered = true;

        // Suppress PHP 8.4+ tempnam() notice so Laravel does not convert it to ErrorException.
        // Must be registered after Laravel's HandleExceptions so this handler runs first.
        set_error_handler(function (int $severity, string $message): bool {
            if (($severity === E_NOTICE || $severity === E_USER_NOTICE)
                && str_contains($message, 'tempnam(): file created in the system\'s temporary directory')) {
                return true;
            }
            return false;
        }, E_NOTICE | E_USER_NOTICE);
    }
}
