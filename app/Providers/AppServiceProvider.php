<?php

namespace App\Providers;

use App\Helpers\Services\LoggingService;
use App\Helpers\Services\QueryingService;
use App\Observers\SalesInvoiceLineObserver;
use App\Observers\SalesInvoiceObserver;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('logging', function ($app) {
            return new LoggingService();
        });

        $this->app->singleton('querying', function ($app) {
            return new QueryingService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupModelObservers();
        Schema::defaultStringLength(191);
    }

    /**
     * Setup the Model class observers
     *
     * @return void
     */
    private function setupModelObservers(): void
    {
        // SalesInvoice::observe(SalesInvoiceObserver::class);
        // SalesInvoiceLine::observe(SalesInvoiceLineObserver::class);
    }

    private function getLogger()
    {
        if (!$this->logger) {
            $this->logger = with(new \Monolog\Logger('api-consumer'))->pushHandler(
                new \Monolog\Handler\RotatingFileHandler(storage_path('logs/api-consumer.log'))
            );
        }

        return $this->logger;
    }
}
