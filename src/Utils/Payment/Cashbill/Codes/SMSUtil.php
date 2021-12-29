<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\SMSClient;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\SMSUtilStrategy;

class SMSUtil implements SMSUtilStrategy
{
    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * [public description]
     * @var SMSClient
     */
    public $client;

    /**
     * Undocumented function
     *
     * @param Config $config
     * @param SMSClient $client
     */
    public function __construct(Config $config, SMSClient $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return isset($this->client->getContents()->active)
            && (bool)$this->client->getContents()->active === true;
    }

    /**
     * [isNumber description]
     * @param  int $number [description]
     * @return bool           [description]
     */
    public function isNumber(int $number): bool
    {
        return isset($this->client->getContents()->number)
            && (int)$this->client->getContents()->number === $number;
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void
    {
        $this->client->get($this->config->get('services.cashbill.code_sms.check_url'), [
            'token' => $attributes['token'],
            'code' => $attributes['code']
        ]);

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS\InactiveCodeException(
                'Code is inactive.',
                403
            );
        }

        if (!$this->isNumber($attributes['number'])) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\SMS\InvalidNumberException(
                'Number is invalid.',
                403
            );
        }
    }
}
