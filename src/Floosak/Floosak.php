<?php

namespace Aymanalhattami\YemeniPaymentGateways\Floosak;

use Aymanalhattami\YemeniPaymentGateways\PaymentGateway;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Floosak extends PaymentGateway
{
    private string|int $otp;

    private string|int|null $requestId = null;
    private string|int $verifyRequestId;

    private float $amount;
    private string $targetPhone;
    private string $purpose;
    private string|int $purchaseId;
    private string|int $transactionId;

    private function getBaseUrl(): string
    {
        $url = rtrim(config('yemeni-payment-gateways.floosak.base_url'), '/');

        return $url . '/';
    }

    private function getPhone(): string
    {
        return config('yemeni-payment-gateways.floosak.phone');
    }

    private function getShortCode(): string
    {
        return config('yemeni-payment-gateways.floosak.short_code');
    }

    private function getWalletId(): int|string
    {
        return config('yemeni-payment-gateways.floosak.wallet_id');
    }

    private function getKey(): string
    {
        return config('yemeni-payment-gateways.floosak.key');
    }

    public function getOtp(): int|string
    {
        return $this->otp;
    }

    public function setOtp(int|string $otp): static
    {
        $this->otp = $otp;

        return $this;
    }

    public function getRequestId(): int|string
    {
        if(is_null($this->requestId)) {
            $this->requestId = time() . rand(1000, 9999);
        }

        return $this->requestId;
    }

    public function setRequestId(int|string $requestId): static
    {
        $this->requestId = $requestId;

        return $this;
    }

    public function getVerifyRequestId(): int|string
    {
        return $this->verifyRequestId;
    }

    public function setVerifyRequestId(int|string $verifyRequestId): static
    {
        $this->verifyRequestId = $verifyRequestId;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): Floosak
    {
        $this->amount = $amount;
        return $this;
    }

    public function getTargetPhone(): string
    {
        return $this->targetPhone;
    }

    public function setTargetPhone(string $targetPhone): Floosak
    {
        $this->targetPhone = $targetPhone;
        return $this;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): Floosak
    {
        $this->purpose = $purpose;
        return $this;
    }

    public function getPurchaseId(): int|string
    {
        return $this->purchaseId;
    }

    public function setPurchaseId(int|string $purchaseId): Floosak
    {
        $this->purchaseId = $purchaseId;
        return $this;
    }

    public function getTransactionId(): int|string
    {
        return $this->transactionId;
    }

    public function setTransactionId(int|string $transactionId): Floosak
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-channel' => 'merchant'
        ];
    }

    private function getAuthorization(): string
    {
        return 'Bearer ' . config('yemeni-payment-gateways.floosak.key');
    }

    public function requestKey(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->post($this->getBaseUrl() . "api/v1/request/key", [
                    'phone' => $this->getPhone(),
                    'short_code' => $this->getShortCode(),
                ]);

            if ($this->response->failed()) {
                throw new RuntimeException($this->response->object()->message);
            }

        } catch (\Exception $e) {
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__ . ' ' . $e->getMessage());
        }

        return $this;
    }

    public function verifyKey(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->post($this->getBaseUrl() . "api/v1/verify/key", [
                    'otp' => $this->getOtp(),
                    'request_id' => $this->getRequestId()
                ]);

            if ($this->response->failed()) {
                throw new RuntimeException($this->response->object()->message);
            }
        } catch (\Exception $e) {
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__ . ' '  . $e->getMessage());
        }

        return $this;
    }

    public function purchase(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->withToken($this->getKey())
                ->post($this->getBaseUrl() . "api/v1/merchant/p2mcl", [
                    'amount' => $this->getAmount(),
                    'source_wallet_id' => $this->getWalletId(),
                    'target_phone' => $this->getTargetPhone(),
                    'purpose' => $this->getPurpose(),
                    'request_id' => $this->getRequestId(),
                ]);

            if ($this->response->failed()) {
                throw new RuntimeException($this->response->object()->message);
            }
        } catch (\Exception $e) {
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__ . ' '  . $e->getMessage());
        }

        return $this;
    }

    public function confirmPurchase(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->withToken($this->getKey())
                ->post($this->getBaseUrl() . "api/v1/merchant/p2mcl/confirm", [
                    'purchase_id' => $this->getTransactionId(),
                    'otp' => $this->getOtp(),
                ]);

            if ($this->response->failed()) {
                throw new RuntimeException($this->response->object()->message);
            }
        } catch (\Exception $e) {
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__ . ' '  . $e->getMessage());
        }

        return $this;
    }

    public function rejectPurchase(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->withToken($this->getKey())
                ->post($this->getBaseUrl() . "api/v1/merchant/p2mcl/reject/" . $this->getPurchaseId());

            if ($this->response->failed()) {
                throw new RuntimeException($this->response->object()->message);
            }
        } catch (\Exception $e) {
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__ . ' '  . $e->getMessage());
        }

        return $this;
    }

    public function refund(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->withToken($this->getKey())
                ->post($this->getBaseUrl() . "api/v1/merchant/p2mcl/refund", [
                    'transaction_id' => $this->getTransactionId(),
                    'amount' => $this->getAmount(),
                    'request_id' => $this->getRequestId()
                ]);

            if ($this->response->failed()) {
                throw new RuntimeException($this->response->object()->message);
            }
        } catch (\Exception $e) {
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__ . ' '  . $e->getMessage());
        }

        return $this;
    }
}