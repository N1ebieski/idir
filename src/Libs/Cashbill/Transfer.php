<?php

namespace N1ebieski\IDir\Libs\Cashbill;

use Illuminate\Contracts\Config\Repository as Config;

/**
 * [Cashbill description]
 */
class Transfer
{
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
     * [__construct description]
     * @param Config $config [description]
     */
    public function __construct(Config $config)
    {
        $this->service = $config->get("services.cashbill.transfer.service");
        $this->transfer_url = $config->get("services.cashbill.transfer.url");
        $this->key = $config->get("services.cashbill.transfer.key");
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
        return md5($this->service.'|'.$this->amount.'||'.$this->desc.'||'.$this->userdata
            .'||||||||||||'.$this->key);
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
        return $this->amount === number_format($amount, 2);
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
     * [verify description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function verify(array $attributes) : void
    {
        if (!$this->isService($attributes['service'])) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Transfer\InvalidServiceException(
                'Invalid service.', 403
            );
        }

        if (!$this->isStatus($attributes['status'])) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Transfer\InvalidStatusException(
                'Invalid status of payment.', 403
            );
        }

        if (!$this->isAmount((float)$attributes['amount'])) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Transfer\InvalidAmountException(
                'Invalid amount of payment.', 403
            );
        }

        if (!$this->isSign($attributes)) {
            throw new \N1ebieski\IDir\Exceptions\Cashbill\Transfer\InvalidSignException(
                'Invalid signature of payment.', 403
            );
        }
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
            'desc' => $this->desc,
            'userdata' => $this->userdata,
            'sign' => $this->makeSign()
        ];
    }

}
