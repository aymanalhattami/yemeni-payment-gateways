<?php

namespace Aymanalhattami\YemeniPaymentGateways;

use Aymanalhattami\Toolbox\Interfaces\MakeInterface;
use Aymanalhattami\Toolbox\Traits\HasMake;

class UnifiedResponse implements MakeInterface
{
    use HasMake;

    private Status $status;

    private bool $success;

    private array $arrayResponse;
    private object $objectResponse;

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function status(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function success(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function getArrayResponse(): array
    {
        return $this->arrayResponse;
    }

    public function arrayResponse(array $response): self
    {
        $this->arrayResponse = $response;
        return $this;
    }

    public function getObjectResponse(): object
    {
        return $this->objectResponse;
    }

    public function objectResponse(object $response): self
    {
        $this->objectResponse = $response;
        return $this;
    }
}