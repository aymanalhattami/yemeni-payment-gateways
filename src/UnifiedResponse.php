<?php

namespace Aymanalhattami\YemeniPaymentGateways;

use Aymanalhattami\Toolbox\Interfaces\MakeInterface;
use Aymanalhattami\Toolbox\Traits\HasMake;

class UnifiedResponse implements MakeInterface
{
    use HasMake;

    private Status $status = Status::Success;

    private bool $success = true;
    private ?string $message = null;

    private array $arrayResponse = [];
    private ?object $objectResponse = null;

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function status(Status $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function success(bool $success): static
    {
        $this->success = $success;
        return $this;
    }

    public function getArrayResponse(): array
    {
        return $this->arrayResponse;
    }

    public function arrayResponse(array $response): static
    {
        $this->arrayResponse = $response;
        return $this;
    }

    public function getObjectResponse(): ?object
    {
        return $this->objectResponse;
    }

    public function objectResponse(object $response): static
    {
        $this->objectResponse = $response;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function message(string $message): static
    {
        $this->message = $message;
        return $this;
    }
}