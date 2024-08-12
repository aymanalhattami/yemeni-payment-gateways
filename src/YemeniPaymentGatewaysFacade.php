<?php

namespace Aymanalhattami\YemeniPaymentGateways;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aymanalhattami\YemeniPaymentGateways\Skeleton\SkeletonClass
 */
class YemeniPaymentGatewaysFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'yemeni-payment-gateways';
    }
}
