<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill;

use Illuminate\Contracts\Config\Repository as Config;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Utils\Payment\Interfaces\TransferUtilStrategy;

/**
 * [Cashbill description]
 */
class TransferUtil implements TransferUtilStrategy
{
    /**
     * [protected description]
     * @var GuzzleClient
     */
    protected $guzzle;

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
    protected $transfer_url;

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
     * @param GuzzleClient $guzzle
     * @param Config $config
     */
    public function __construct(GuzzleClient $guzzle, Config $config)
    {
        $this->guzzle = $guzzle;

        $this->service = $config->get("services.cashbill.transfer.service");
        $this->transfer_url = $config->get("services.cashbill.transfer.url");
        $this->key = $config->get("services.cashbill.transfer.key");
        $this->currency = $config->get("services.cashbill.transfer.currency");
        $this->lang = $config->get("services.cashbill.transfer.lang");
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment() : string
    {
        $redirects = $this->response->getHeader(\GuzzleHttp\RedirectMiddleware::HISTORY_HEADER);

        return end($redirects);
    }

    /**
     * [setup description]
     * @param  array $attributes [description]
     * @return static              [description]
     */
    public function setup(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * [makeSign description]
     * @return string [description]
     */
    public function makeSign() : string
    {
        return md5($this->service.'|'.$this->amount.'|'.$this->currency.'|'
            .$this->desc.'|'.$this->lang.'|'.$this->userdata.'||||||||||||'.$this->key);
    }

    /**
     * [isService description]
     * @param  string $service [description]
     * @return bool            [description]
     */
    public function isService(string $service) : bool
    {
        return $this->service === $service;
    }

    /**
     * [isAmount description]
     * @param  float $amount [description]
     * @return bool          [description]
     */
    public function isAmount(float $amount) : bool
    {
        return $this->amount === number_format($amount, 2, '.', '');
    }

    /**
     * [isSign description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function isSign(array $attributes) : bool
    {
        return md5($this->service.$attributes['orderid'].$attributes['amount']
            .$attributes['userdata'].$attributes['status'].$this->key) === $attributes['sign'];
    }

    /**
     * [isStatus description]
     * @param  string $status [description]
     * @return bool           [description]
     */
    public function isStatus(string $status) : bool
    {
        return $status === 'ok';
    }

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes) : void
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
     * Undocumented function
     *
     * @return GuzzleResponse
     */
    public function makeResponse() : GuzzleResponse
    {
        try {
            $this->response = $this->guzzle->request('POST', $this->transfer_url, [
                'allow_redirects' => ['track_redirects' => true],
                'form_params' => $this->all()
            ]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Exception(
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $this->response;
    }

    /**
     * [all description]
     * @return array [description]
     */
    public function all() : array
    {
        return [
            'service' => $this->service,
            'transfer_url' => $this->transfer_url,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lang' => $this->lang,
            'desc' => $this->desc,
            'userdata' => $this->userdata,
            'sign' => $this->makeSign()
        ];
    }
}
