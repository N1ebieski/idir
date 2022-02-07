<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill;

use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\TransferClient;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;

class TransferUtil implements TransferUtilStrategy
{
    /**
     * [protected description]
     * @var TransferClient
     */
    public $client;

    /**
     * [protected description]
     * @var Config
     */
    protected $config;

    /**
     * [protected description]
     * @var GuzzleResponse
     */
    protected $response;

    /**
     * [protected description]
     * @var string
     */
    protected $service;

    /**
     * [protected description]
     * @var string
     */
    protected $key;

    /**
     * [protected description]
     * @var float
     */
    protected $amount;

    /**
     * [protected description]
     * @var string
     */
    protected $currency;

    /**
     * [protected description]
     * @var string
     */
    protected $lang;

    /**
     * [protected description]
     * @var string
     */
    protected $desc;

    /**
     * [protected description]
     * @var string
     */
    protected $uuid = null;

    /**
     * [protected description]
     * @var string
     */
    protected $redirect = null;

    /**
     * [protected description]
     * @var int
     */
    protected $userdata;

    /**
     * [protected description]
     * @var string
     */
    protected $sign;

    /**
     * Undocumented function
     *
     * @param TransferClient $client
     * @param Config $config
     */
    public function __construct(TransferClient $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;

        $this->service = $config->get("services.cashbill.transfer.service");
        $this->key = $config->get("services.cashbill.transfer.key");
        $this->currency = $config->get("services.cashbill.transfer.currency");
        $this->lang = $config->get("services.cashbill.transfer.lang");
    }

    /**
     * Undocumented function
     *
     * @param string $amount
     * @return self
     */
    public function setAmount(string $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setDesc(string $desc)
    {
        $this->desc = $desc;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;

        $this->userdata = json_encode([
            'uuid' => $this->uuid,
            'redirect' => $this->redirect
        ]);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setRedirect(string $redirect)
    {
        $this->redirect = $redirect;

        $this->userdata = json_encode([
            'uuid' => $this->uuid,
            'redirect' => $this->redirect
        ]);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setCancelUrl(string $cancelUrl)
    {
        //

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setReturnUrl(string $returnUrl)
    {
        //

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setNotifyUrl(string $notifyUrl)
    {
        //

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment(): string
    {
        $redirects = $this->client->getResponse()->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function sign(): string
    {
        return md5($this->service . '|' . $this->amount . '|' . $this->currency . '|'
            . $this->desc . '|' . $this->lang . '|' . $this->userdata . '||||||||||||' . $this->key);
    }

    /**
     * [isService description]
     * @param  string $service [description]
     * @return bool            [description]
     */
    public function isService(string $service): bool
    {
        return $this->service === $service;
    }

    /**
     * [isAmount description]
     * @param  float $amount [description]
     * @return bool          [description]
     */
    public function isAmount(float $amount): bool
    {
        return $this->amount === number_format($amount, 2, '.', '');
    }

    /**
     * [isSign description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function isSign(array $attributes): bool
    {
        return md5($this->service . $attributes['orderid'] . $attributes['amount']
            . $attributes['userdata'] . $attributes['status'] . $this->key) === $attributes['sign'];
    }

    /**
     * [isStatus description]
     * @param  string $status [description]
     * @return bool           [description]
     */
    public function isStatus(string $status): bool
    {
        return $status === 'ok';
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function purchase(): void
    {
        $this->client->post($this->config->get("services.cashbill.transfer.url"), $this->all());
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function complete(array $attributes): void
    {
        if (!$this->isStatus($attributes['status'])) {
            return;
        }

        if (!$this->isSign($attributes)) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException();
        }
    }

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes): void
    {
        if (!$this->isService($attributes['service'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidServiceException();
        }

        if (!$this->isStatus($attributes['status'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidStatusException();
        }

        if (!$this->isAmount((float)$attributes['amount'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidAmountException();
        }

        if (!$this->isSign($attributes)) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException();
        }
    }

    /**
     * [all description]
     * @return array [description]
     */
    public function all(): array
    {
        return [
            'service' => $this->service,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lang' => $this->lang,
            'desc' => $this->desc,
            'userdata' => $this->userdata,
            'sign' => $this->sign()
        ];
    }
}
