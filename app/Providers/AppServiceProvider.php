<?php

namespace App\Providers;

use App\Http\Middleware\LogApiRequests;
use App\Services\DailyCommissionService;
use App\Services\IpWhitelistService;
use Illuminate\Support\ServiceProvider;
use App\Services\RequestLogger;
use App\Services\SubsequentRequests;
use Illuminate\Pagination\Paginator;
use App\Services\DatabaseConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        'ip-checker' => IpWhitelistService::class,
        'subsequent-requests' => SubsequentRequests::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        'request-logger' => RequestLogger::class,
        LogApiRequests::class => LogApiRequests::class,
        'database-config' => DatabaseConfig::class,
        'daily-commission' => DailyCommissionService::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
