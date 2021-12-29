<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill;

use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\CheckTransferClient;

class TransferUtil implements TransferUtilStrategy
{
    /**
     * [protected description]
     * @var CheckTransferClient
     */
    protected $checkClient;

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
     * @param CheckTransferClient $checkClient
     * @param Config $config
     */
    public function __construct(CheckTransferClient $checkClient, Config $config)
    {
        $this->checkClient = $checkClient;

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
        $redirects = $this->checkClient->getResponse()->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

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
        $this->checkClient->request(null, $this->all());
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
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException(
                'Invalid signature of payment.',
                403
            );
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
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidServiceException(
                'Invalid service.',
                403
            );
        }

        if (!$this->isStatus($attributes['status'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidStatusException(
                'Invalid status of payment.',
                403
            );
        }

        if (!$this->isAmount((float)$attributes['amount'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidAmountException(
                'Invalid amount of payment.',
                403
            );
        }

        if (!$this->isSign($attributes)) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Transfer\InvalidSignException(
                'Invalid signature of payment.',
                403
            );
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
