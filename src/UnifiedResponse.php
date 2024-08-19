<?php

namespace Aymanalhattami\YemeniPaymentGateways;

use Aymanalhattami\Toolbox\Interfaces\MakeInterface;
use Aymanalhattami\Toolbox\Traits\HasMake;

class UnifiedResponse implements MakeInterface
{
    use HasMake;

    private string $status = Status::Success->value;

    private bool $success = true;
    private ?string $message = null;

    private array $data = [];

    public function getStatus(): string
    {
        return $this->status;
    }

    public function status(string $status): static
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

    public function getData(): array
    {
        return $this->data;
    }

    public function data(array $data): static
    {
        $this->data = $data;
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