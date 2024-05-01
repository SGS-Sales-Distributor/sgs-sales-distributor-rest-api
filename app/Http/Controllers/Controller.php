<?php

namespace App\Http\Controllers;

use App\Repositories\BasicAuthentication\BasicAuthInterface;
use App\Repositories\JwtAuthentication\JwtAuthInterface;
use App\Repositories\MasterCallPlanInterface;
use App\Repositories\MasterStatusInterface;
use App\Repositories\MasterTargetNooInterface;
use App\Repositories\ProductInfoInterface;
use App\Repositories\ProgramInterface;
use App\Repositories\SalesmanInterface;
use App\Repositories\StoreInterface;

abstract class Controller
{
    protected JwtAuthInterface $jwtAuthInterface;
    protected BasicAuthInterface $basicAuthInterface;
    protected StoreInterface $storeInterface;
    protected MasterTargetNooInterface $masterTargetNooInterface;
    protected MasterCallPlanInterface $masterCallPlanInterface;
    protected ProgramInterface $programInterface;
    protected ProductInfoInterface $productInfoInterface;
    protected MasterStatusInterface $masterStatusInterface;
    protected SalesmanInterface $salesmanInterface;

    public function __construct(
        JwtAuthInterface $jwtAuthInterface,
        BasicAuthInterface $basicAuthInterface,
        StoreInterface $storeInterface,
        MasterTargetNooInterface $masterTargetNooInterface,
        MasterCallPlanInterface $masterCallPlanInterface,
        ProgramInterface $programInterface,
        ProductInfoInterface $productInfoInterface,
        MasterStatusInterface $masterStatusInterface,
        SalesmanInterface $salesmanInterface,
    )
    {
        $this->jwtAuthInterface = $jwtAuthInterface;
        $this->basicAuthInterface = $basicAuthInterface;
        $this->storeInterface = $storeInterface;
        $this->masterTargetNooInterface = $masterTargetNooInterface;
        $this->masterCallPlanInterface = $masterCallPlanInterface;
        $this->programInterface = $programInterface;
        $this->productInfoInterface = $productInfoInterface;
        $this->masterStatusInterface = $masterStatusInterface;
        $this->salesmanInterface = $salesmanInterface;
    }
}
