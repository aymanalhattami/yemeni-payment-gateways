<?php

namespace Aymanalhattami\YemeniPaymentGateways\Jawali;

use Aymanalhattami\Toolbox\EnvEditor;
use Aymanalhattami\YemeniPaymentGateways\PaymentGateway;
use Aymanalhattami\YemeniPaymentGateways\Status;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Jawali extends PaymentGateway
{
    private string|int $voucher;
    private string|int $receiverPhone;
    private string $purpose;
    private string|int $refId;
    private int|float $amount;
    private string $currency;

    private function getBaseUrl(): string
    {
        $url = rtrim(config('yemeni-payment-gateways.jawali.base_url'), '/');

        return $url . '/';
    }

    private function getPassword(): string
    {
        return config('yemeni-payment-gateways.jawali.password');
    }

    private function getOrgId(): string|int
    {
        return config('yemeni-payment-gateways.jawali.org_id');
    }

    private function getUserId(): string
    {
        return (string) config('yemeni-payment-gateways.jawali.user_id');
    }

    private function getTimestampInMs(): int
    {
        return (int) Carbon::now()->getTimestampMs();
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

    private function getWalletToken(): string|int
    {
        return config('yemeni-payment-gateways.jawali.wallet_token');
    }

    public function voucher(string|int $voucher): static
    {
        $this->voucher = $voucher;

        return $this;
    }

    private function getVoucher(): string|int
    {
        return $this->voucher;
    }

    public function receiverPhone(string|int $receiverPhone): static
    {
        $this->receiverPhone = $receiverPhone;

        return $this;
    }

    private function getReceiverPhone(): string|int
    {
        return $this->receiverPhone;
    }

    public function purpose(string $purpose): static
    {
        $this->purpose = $purpose;

        return $this;
    }

    private function getPurpose(): string|int
    {
        return $this->purpose;
    }

    public function refId(string|int $refId): static
    {
        $this->refId = $refId;

        return $this;
    }

    private function getRefId(): string
    {
        return (string) $this->refId;
    }

    public function amount(int|float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    private function getAmount():string
    {
        return (string) $this->amount;
    }

    public function currency($currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    private function getCurrency(): string
    {
        return $this->currency;
    }

    public function login(): static
    {
        try {
            $this->response = Http::asForm()->post($this->getBaseUrl() . 'paygate/oauth/token', [
                'username' => $this->getUserId(),
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
                if($this->response->object()->responseStatus->systemStatus == -1) {
                    $this->unifiedResponse
                        ->status(Status::Failed->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } elseif($this->response->object()->responseStatus->systemStatus == 0) {
                    $this->unifiedResponse
                        ->status(Status::Success->value)
                        ->success(true)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } else {
                    $this->unifiedResponse
                        ->status(Status::Unknown->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                }
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

    public function ecommerceInquiry(): static
    {
        try {
            $data = [
                "header" => [
                    "serviceDetail" => [
                        "corrID" => "59ba381c-1f5f-4480-90cc-0660b9cc850e",
                        "domainName" => "MerchantDomain",
                        "serviceName" => "PAYAG.ECOMMERCEINQUIRY",
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
                    "agentWallet" => $this->getAgentWallet(),
                    "password" => $this->getAgentWalletPassword(),
                    "accessToken" => $this->getWalletToken(),
                    "voucher" => $this->getVoucher(),
                    "receiverMobile" => $this->getReceiverPhone(),
                    "purpose" => $this->getPurpose(),
                    "refId" => $this->getRefId(),
                ],
            ];

            $this->response = Http::asJson()
                ->withToken($this->getLoginToken())
                ->post($this->getBaseUrl() . 'paygate/v1/ws/callWS', $data);

            if ($this->response->failed()) {
                throw new Exception($this->response->object()?->error_description);
            } else {
                if($this->response->object()->responseStatus->systemStatus == -1) {
                    $this->unifiedResponse
                        ->status(Status::Failed->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } elseif($this->response->object()->responseStatus->systemStatus == 0) {
                    $this->unifiedResponse
                        ->status(Status::Success->value)
                        ->success(true)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } else {
                    $this->unifiedResponse
                        ->status(Status::Unknown->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                }
            }
        } catch (Exception $e) {
            $this->unifiedResponse
                ->status(Status::Failed->value)
                ->success(false)
                ->message($e->getMessage())
                ->data($this->response->json() ?? []);
        }

        return $this;
    }

    public function ecommerceCashOut(): static
    {
        try {
            $data = [
                "header" => [
                    "serviceDetail" => [
                        "corrID" => "59ba381c-1f5f-4480-90cc-0660b9cc850e",
                        "domainName" => "MerchantDomain",
                        "serviceName" => "PAYAG.ECOMMCASHOUT",
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
                    "agentWallet" => $this->getAgentWallet(),
                    "password" => $this->getAgentWalletPassword(),
                    "accessToken" => $this->getWalletToken(),
                    "amount" => $this->getAmount(),
                    "currency" => $this->getCurrency(),
                    "voucher" => $this->getVoucher(),
                    "receiverMobile" => $this->getReceiverPhone(),
                    "purpose" => $this->getPurpose(),
                    "refId" => $this->getRefId(),
                ],
            ];


            $this->response = Http::asJson()
                ->withToken($this->getLoginToken())
                ->post($this->getBaseUrl() . 'paygate/v1/ws/callWS', $data);

            if ($this->response->failed()) {
                throw new Exception($this->response->object()?->error_description);
            } else {
                if($this->response->object()->responseStatus->systemStatus == -1) {
                    $this->unifiedResponse
                        ->status(Status::Failed->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } elseif($this->response->object()->responseStatus->systemStatus == 0) {
                    $this->unifiedResponse
                        ->status(Status::Success->value)
                        ->success(true)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } else {
                    $this->unifiedResponse
                        ->status(Status::Unknown->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                }
            }
        } catch (Exception $e) {
            $this->unifiedResponse
                ->status(Status::Failed->value)
                ->success(false)
                ->message($e->getMessage())
                ->data($this->response->json() ?? []);
        }

        return $this;
    }

    public function agentCheckStatus(): static
    {
        try {
            $data = [
                "header" => [
                    "serviceDetail" => [
                        "corrID" => "59ba381c-1f5f-4480-90cc-0660b9cc850e",
                        "domainName" => "WalletDomain",
                        "serviceName" => "PAYWA.AGENTCHECKSTATUS",
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
                    "agentWallet" => $this->getAgentWallet(),
                    "password" => $this->getAgentWalletPassword(),
                    "refId" => $this->getRefId(),
                ],
            ];

            $this->response = Http::asJson()
                ->withToken($this->getLoginToken())
                ->post($this->getBaseUrl() . 'paygate/v1/ws/callWS', $data);

            if ($this->response->failed()) {
                throw new Exception($this->response->object()?->error_description);
            } else {
                if($this->response->object()->responseStatus->systemStatus == -1) {
                    $this->unifiedResponse
                        ->status(Status::Failed->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } elseif($this->response->object()->responseStatus->systemStatus == 0) {
                    $this->unifiedResponse
                        ->status(Status::Success->value)
                        ->success(true)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                } else {
                    $this->unifiedResponse
                        ->status(Status::Unknown->value)
                        ->success(false)
                        ->message($this->response->object()->responseStatus->systemStatusDescNative)
                        ->data($this->response->json());
                }
            }
        } catch (Exception $e) {
            $this->unifiedResponse
                ->status(Status::Failed->value)
                ->success(false)
                ->message($e->getMessage())
                ->data($this->response->json() ?? []);
        }

        return $this;
    }

    public function storeLoginTokenToEnv(): static
    {
        if($this->getResponse()->successful()) {
            EnvEditor::make()->set('JAWALI_LOGIN_TOKEN', $this->getResponse()->object()?->access_token);
        }

        return $this;
    }

    public function storeWalletTokenToEnv(): static
    {
        if($this->getResponse()->successful()) {
            EnvEditor::make()->set('JAWALI_WALLET_TOKEN', $this->getResponse()->object()?->responseBody->access_token);
        }

        return $this;
    }

    public function storeRefreshTokenToEnv(): static
    {
        if($this->getResponse()->successful()) {
            EnvEditor::make()->set('JAWALI_REFRESH_TOKEN', $this->getResponse()->object()?->refresh_token);
        }

        return $this;
    }
}