<?php

namespace Aymanalhattami\YemeniPaymentGateways\Floosak;

use Aymanalhattami\YemeniPaymentGateways\PaymentGateway;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Floosak extends PaymentGateway
{
    private string|int $otp;

    private string|int $requestId;

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
        return $this->requestId;
    }

    public function setRequestId(int|string $requestId): static
    {
        $this->requestId = $requestId;

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

    /**
     * @throws ConnectionException
     */
    public function requestKey(): static
    {
        $this->response = Http::withHeaders($this->getHeaders())
            ->post($this->getBaseUrl() . "api/v1/request/key", [
                'phone' => $this->getPhone(),
                'short_code' => $this->getShortCode()
            ]);

        return $this;
    }

    /**
     * @throws ConnectionException
     */
    public function verifyKey(): static
    {
        try {
            $this->response = Http::withHeaders($this->getHeaders())
                ->post($this->getBaseUrl() . "api/v1/request/ke", [
                    'phone' => $this->getPhone(),
                    'short_code' => $this->getShortCode(),
                ]);

            // Optional: You might want to handle the response here, like logging or processing it.
            if ($this->response->successful()) {
                $this->requestId = $this->response->object()->request_id;
            } else {

            }
        } catch (\Exception $e) {
            // Handle the exception, log error, or throw a custom exception
            throw new RuntimeException(__CLASS__ . '::' . __FUNCTION__  . $e->getMessage());
        }

        return $this;
    }

    /**
     * @throws ConnectionException
     */
    public function requestAndVerifyKey(): static
    {
        $requestId = $this->requestKey()->getResponse()->object()->request_id;
        $this
            ->setRequestId($this->getRequestId())
            ->setOtp($this->getOtp())
            ->verifyKey();

        return $this;
    }

    /**
     * @throws ConnectionException
     */
    public function pay(float|int $amount): static
    {
        $this->response = Http::withHeaders($this->getHeaders())
            ->post($this->getBaseUrl() . "api/v1/wallet/pay", [
                'amount' => $amount,
                'wallet_id' => $this->getWalletId(),
                'key' => $this->getKey()
            ]);

        return $this;
    }
}