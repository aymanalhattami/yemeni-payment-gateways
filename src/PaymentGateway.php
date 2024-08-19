<?php

namespace Aymanalhattami\YemeniPaymentGateways;

use Aymanalhattami\Toolbox\Interfaces\MakeInterface;
use Aymanalhattami\Toolbox\Traits\HasMake;
use Aymanalhattami\YemeniPaymentGateways\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Client\Response;

abstract class PaymentGateway implements PaymentGatewayInterface, MakeInterface
{
    use HasMake;

    protected Response $response;

    protected UnifiedResponse $unifiedResponse;

    public function __construct()
    {
        $this->unifiedResponse = new UnifiedResponse();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getUnifiedResponse(): UnifiedResponse
    {
        return $this->unifiedResponse;
    }
}