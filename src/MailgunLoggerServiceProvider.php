<?php


namespace Aleahy\MailgunLogger;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MailgunLoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mailgun-logger.php' => config_path('mailgun-logger.php'),
            ], 'config');
        }


        Route::macro('mailgunWebhooks', function (string $url) {
            return Route::post($url, MailgunLoggerWebhookController::class)->name('mailgunlogger.webhooks');
        });
    }
}