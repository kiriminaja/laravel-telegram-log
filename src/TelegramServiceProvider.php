<?php

namespace TelegramLog;

use Illuminate\Support\ServiceProvider;

/*
 * Class TelegramLoggerServiceProvider
 * @package App\TelegramLog
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/telegram-log.php', 'telegram-log');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/views', 'laravel-telegram-log');
        $this->publishes([__DIR__.'/views' => base_path('resources/views/vendor/laravel-telegram-log')], 'views');
        $this->publishes([__DIR__ . '/../config/telegram-log.php' => config_path('telegram-log.php')], 'config');
    }
}
