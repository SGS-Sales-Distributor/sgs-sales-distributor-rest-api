<?php

namespace App\Providers;

use App\Repositories\BasicAuthentication\BasicAuthInterface;
use App\Repositories\BasicAuthentication\BasicAuthRepository;
use App\Repositories\MasterCallPlanInterface;
use App\Repositories\MasterCallPlanRepository;
use App\Repositories\MasterStatusInterface;
use App\Repositories\MasterStatusRepository;
use App\Repositories\MasterTargetNooInterface;
use App\Repositories\MasterTargetNooRepository;
use App\Repositories\MasterTypeProgramInterface;
use App\Repositories\MasterTypeProgramRepository;
use App\Repositories\StoreInterface;
use App\Repositories\StoreRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BasicAuthInterface::class, BasicAuthRepository::class);
        $this->app->bind(MasterCallPlanInterface::class, MasterCallPlanRepository::class);
        $this->app->bind(MasterTargetNooInterface::class, MasterTargetNooRepository::class);
        $this->app->bind(MasterTypeProgramInterface::class, MasterTypeProgramRepository::class);
        $this->app->bind(MasterStatusInterface::class, MasterStatusRepository::class);
        $this->app->bind(StoreInterface::class, StoreRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
