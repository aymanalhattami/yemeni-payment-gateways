<?php

namespace Aymanalhattami\YemeniPaymentGateways;

class UnifiedResponse
{
    private Status $status;

    private bool $success;

    private array $data;

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

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
}