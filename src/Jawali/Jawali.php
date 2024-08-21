<?php

namespace Aymanalhattami\YemeniPaymentGateways\Jawali;

use Aymanalhattami\YemeniPaymentGateways\PaymentGateway;
use Aymanalhattami\YemeniPaymentGateways\Status;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Jawali extends PaymentGateway
{
    private function getBaseUrl(): string
    {
        $url = rtrim(config('yemeni-payment-gateways.jawali.base_url'), '/');

        return $url . '/';
    }


    public function login(): static
    {
        try {
            $this->response = Http::asForm()->post($this->getBaseUrl() . 'paygate/oauth/token', [
                'username' => $this->getUsername(),
                'password' => $this->getPassword(),
                'grant_type' => 'password',
                'client_id' => 'restapp',
                'client_secret' => 'restapp',
                'scope' => 'read',
            ]);

            if ($this->response->failed()) {
                throw new Exception($this->response->object()->error_description);
            } else {
                $this->unifiedResponse
                    ->status(Status::Success->value)
                    ->success(true)
                    ->data($this->response->json());
            }
        } catch (Exception $e) {
            $this->unifiedResponse
                ->status(Status::Failed->value)
                ->success(false)
                ->message($e->getMessage())
                ->data($this->response->json());
        }


        return $this;
    }

    private function getUsername(): string
    {
        return config('yemeni-payment-gateways.jawali.user_id');
    }

    private function getPassword(): string
    {
        return config('yemeni-payment-gateways.jawali.password');
    }
}