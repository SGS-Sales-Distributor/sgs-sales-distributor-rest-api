<?php

namespace App\Http\Controllers;

use App\Repositories\BasicAuthentication\BasicAuthInterface;
use App\Repositories\MasterCallPlanInterface;
use App\Repositories\MasterStatusInterface;
use App\Repositories\MasterTargetNooInterface;
use App\Repositories\MasterTypeProgramInterface;
use App\Repositories\StoreInterface;

abstract class Controller
{
    protected BasicAuthInterface $basicAuthInterface;
    protected StoreInterface $storeInterface;
    protected MasterTargetNooInterface $masterTargetNooInterface;
    protected MasterCallPlanInterface $masterCallPlanInterface;
    protected MasterTypeProgramInterface $masterTypeProgramInterface;
    protected MasterStatusInterface $masterStatusInterface;

    public function __construct(
        BasicAuthInterface $basicAuthInterface,
        StoreInterface $storeInterface,
        MasterTargetNooInterface $masterTargetNooInterface,
        MasterCallPlanInterface $masterCallPlanInterface,
        MasterTypeProgramInterface $masterTypeProgramInterface,
        MasterStatusInterface $masterStatusInterface,
    )
    {
        $this->basicAuthInterface = $basicAuthInterface;
        $this->storeInterface = $storeInterface;
        $this->masterTargetNooInterface = $masterTargetNooInterface;
        $this->masterCallPlanInterface = $masterCallPlanInterface;
        $this->masterTypeProgramInterface = $masterTypeProgramInterface;
        $this->masterStatusInterface = $masterStatusInterface;
    }
}
