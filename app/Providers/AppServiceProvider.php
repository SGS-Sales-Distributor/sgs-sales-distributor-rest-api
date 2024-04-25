<?php

namespace App\Providers;

use App\Repositories\BasicAuthentication\BasicAuthInterface;
use App\Repositories\BasicAuthentication\BasicAuthRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BasicAuthInterface::class, BasicAuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
