<?php

namespace Aymanalhattami\YemeniPaymentGateways\Jawali;

use Aymanalhattami\Toolbox\EnvEditor;
use Aymanalhattami\YemeniPaymentGateways\PaymentGateway;
use Aymanalhattami\YemeniPaymentGateways\Status;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Jawali extends PaymentGateway
{
    private function getBaseUrl(): string
    {
        $url = rtrim(config('yemeni-payment-gateways.jawali.base_url'), '/');

        return $url . '/';
    }

    private function getUsername(): string
    {
        return config('yemeni-payment-gateways.jawali.user_id');
    }

    private function getPassword(): string
    {
        return config('yemeni-payment-gateways.jawali.password');
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
                throw new Exception($this->response->object()?->error_description);
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

    public function walletAuthentication(): static
    {
        try {
            $data = [
                "header" => [
                    "serviceDetail" => [
                        "corrID" => "59ba381c-1f5f-4480-90cc-0660b9cc850e",
                        "domainName" => "WalletDomain",
                        "serviceName" => "PAYWA.WALLETAUTHENTICATION",
                    ],
                    "signonDetail" => [
                        "clientID" => "WeCash",
                        "orgID" => $this->getOrgId(),
                        "userID" => $this->getUserId(),
                        "externalUser" => "user1",
                    ],
                    "messageContext" => [
                        "clientDate" => $this->getTimestampInMs(),
                        "bodyType" => "Clear",
                    ],
                ],
                "body" => [
                    "identifier" => $this->getAgentWallet(),
                    "password" => $this->getAgentWalletPassword(),
                ],
            ];

            $this->response = Http::asJson()
                ->withToken($this->getLoginToken())
                ->post($this->getBaseUrl() . 'paygate/v1/ws/callWS', $data);

            if ($this->response->failed()) {
                throw new Exception($this->response->object()?->error_description);
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

    public function storeLoginAccessTokenToEnv(): static
    {
        EnvEditor::make()->set('JAWALI_ACCESS_TOKEN', $this->getResponse()->object()?->access_token);

        return $this;
    }

    public function storeLoginTokenToEnv(): static
    {
        EnvEditor::make()->set('JAWALI_LOGIN_TOKEN', $this->getResponse()->object()?->access_token);

        return $this;
    }

    public function storeRefreshTokenToEnv(): static
    {
        EnvEditor::make()->set('JAWALI_REFRESH_TOKEN', $this->getResponse()->object()?->refresh_token);

        return $this;
    }

    private function getOrgId(): string|int
    {
        return config('yemeni-payment-gateways.jawali.org_id');
    }

    private function getUserId(): string|int
    {
        return config('yemeni-payment-gateways.jawali.user_id');
    }

    private function getTimestampInMs(): string|int
    {
        return Carbon::now()->getTimestampMs();
    }

    private function getAgentWallet(): string|int
    {
        return config('yemeni-payment-gateways.jawali.agent_wallet');
    }

    private function getAgentWalletPassword(): string|int
    {
        return config('yemeni-payment-gateways.jawali.agent_wallet_pwd');
    }

    private function getLoginToken(): string|int
    {
        return config('yemeni-payment-gateways.jawali.login_token');
    }


}