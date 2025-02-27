<?php

namespace App\Http\Controllers;

use App\Repositories\AdminInterface;
use App\Repositories\BasicAuthentication\BasicAuthInterface;
use App\Repositories\BrandInterface;
use App\Repositories\JwtAuthentication\JwtAuthInterface;
use App\Repositories\MasterCallPlanInterface;
use App\Repositories\MasterTargetNooInterface;
use App\Repositories\KodeLokasiInterface;
use App\Repositories\ProductInterface;
use App\Repositories\ProgramInterface;
use App\Repositories\ProgramTypeInterface;
use App\Repositories\SalesmanInterface;
use App\Repositories\StoreInterface;
use App\Repositories\ProfilNotvisitInterface;
use App\Repositories\StoreCabangInterface;
use App\Repositories\OrderCustomerSalesInterface;
use App\Traits\ApiResponse;

abstract class Controller
{
    use ApiResponse;
    
    protected JwtAuthInterface $jwtAuthInterface;
    protected BasicAuthInterface $basicAuthInterface;
    protected StoreInterface $storeInterface;
    protected MasterTargetNooInterface $masterTargetNooInterface;
    protected MasterCallPlanInterface $masterCallPlanInterface;
    protected ProgramInterface $programInterface;
    protected ProgramTypeInterface $programTypeInterface;
    protected SalesmanInterface $salesmanInterface;
    protected AdminInterface $adminInterface;
    protected ProductInterface $productInterface;
    protected BrandInterface $brandInterface;
    protected KodeLokasiInterface $kodeLokasiInterface;
    protected profilNotvisitInterface $profilNotvisitInterface;
    protected StoreCabangInterface $StoreCabangInterface;
    protected OrderCustomerSalesInterface $OrderCustomerSalesInterface;
    

    public function __construct(
        JwtAuthInterface $jwtAuthInterface,
        BasicAuthInterface $basicAuthInterface,
        StoreInterface $storeInterface,
        MasterTargetNooInterface $masterTargetNooInterface,
        MasterCallPlanInterface $masterCallPlanInterface,
        ProgramInterface $programInterface,
        ProgramTypeInterface $programTypeInterface,
        SalesmanInterface $salesmanInterface,
        AdminInterface $adminInterface,
        ProductInterface $productInterface,
        BrandInterface $brandInterface,
        KodeLokasiInterface $kodeLokasiInterface,
        profilNotvisitInterface $profilNotvisitInterface,
        StoreCabangInterface $StoreCabangInterface,
        OrderCustomerSalesInterface $OrderCustomerSalesInterface,
    )
    {
        $this->jwtAuthInterface = $jwtAuthInterface;
        $this->basicAuthInterface = $basicAuthInterface;
        $this->storeInterface = $storeInterface;
        $this->masterTargetNooInterface = $masterTargetNooInterface;
        $this->masterCallPlanInterface = $masterCallPlanInterface;
        $this->programInterface = $programInterface;
        $this->programTypeInterface = $programTypeInterface;
        $this->salesmanInterface = $salesmanInterface;
        $this->adminInterface = $adminInterface;
        $this->productInterface = $productInterface;
        $this->brandInterface = $brandInterface;
        $this->kodeLokasiInterface = $kodeLokasiInterface;
        $this->profilNotvisitInterface = $profilNotvisitInterface;
        $this->StoreCabangInterface = $StoreCabangInterface;
        $this->OrderCustomerSalesInterface = $OrderCustomerSalesInterface;
    }
}
