<?php

namespace App\Providers;

use App\Interfaces\LogRepositoryInterface;
use App\Repositories\MySQLLogRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(LogRepositoryInterface::class, MySQLLogRepository::class);
    }
}
