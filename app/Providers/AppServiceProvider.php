<?php

namespace App\Providers;

use App\Repositories\AdminInterface;
use App\Repositories\AdminRepository;
use App\Repositories\BasicAuthentication\BasicAuthInterface;
use App\Repositories\BasicAuthentication\BasicAuthRepository;
use App\Repositories\BrandInterface;
use App\Repositories\BrandRepository;
use App\Repositories\JwtAuthentication\JwtAuthInterface;
use App\Repositories\JwtAuthentication\JwtAuthRepository;
use App\Repositories\KodeLokasiInterface;
use App\Repositories\KodeLokasiRepository;
use App\Repositories\MasterCallPlanInterface;
use App\Repositories\MasterCallPlanRepository;
use App\Repositories\MasterTargetNooInterface;
use App\Repositories\MasterTargetNooRepository;
use App\Repositories\ProductInterface;
use App\Repositories\ProductRepository;
use App\Repositories\ProfilNotvisitInterface;
use App\Repositories\ProfilNotvisitRepository;
use App\Repositories\ProgramInterface;
use App\Repositories\ProgramRepository;
use App\Repositories\ProgramTypeInterface;
use App\Repositories\ProgramTypeRepository;
use App\Repositories\SalesmanInterface;
use App\Repositories\SalesmanRepository;
use App\Repositories\StoreInterface;
use App\Repositories\StoreRepository;
use App\Repositories\StoreCabangRepository;
use App\Repositories\StoreCabangInterface;
use App\Repositories\OrderCustomerSalesInterface;
use App\Repositories\OrderCustomerSalesRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(JwtAuthInterface::class, JwtAuthRepository::class);
        $this->app->bind(BasicAuthInterface::class, BasicAuthRepository::class);
        $this->app->bind(MasterCallPlanInterface::class, MasterCallPlanRepository::class);
        $this->app->bind(MasterTargetNooInterface::class, MasterTargetNooRepository::class);
        $this->app->bind(ProgramInterface::class, ProgramRepository::class);
        $this->app->bind(ProgramTypeInterface::class, ProgramTypeRepository::class);
        $this->app->bind(StoreInterface::class, StoreRepository::class);
        $this->app->bind(SalesmanInterface::class, SalesmanRepository::class);
        $this->app->bind(AdminInterface::class, AdminRepository::class);
        $this->app->bind(ProductInterface::class, ProductRepository::class);
        $this->app->bind(BrandInterface::class, BrandRepository::class);
        $this->app->bind(KodeLokasiInterface::class, KodeLokasiRepository::class);
        $this->app->bind(ProfilNotvisitInterface::class,ProfilNotvisitRepository::class);
        $this->app->bind(StoreCabangInterface::class,StoreCabangRepository::class);
        $this->app->bind(orderCustomerSalesInterface::class, OrderCustomerSalesRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
