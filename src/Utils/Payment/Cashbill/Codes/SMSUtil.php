<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\SMSUtilStrategy;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\CheckSMSClient;

class SMSUtil implements SMSUtilStrategy
{
    /**
     * [public description]
     * @var CheckSMSClient
     */
    public $checkClient;

    /**
     * Undocumented function
     *
     * @param Config $config
     * @param CheckSMSClient $checkClient
     */
    public function __construct(Config $config, CheckSMSClient $checkClient)
    {
        $this->check_url = $config->get('services.cashbill.code_sms.check_url');

        $this->checkClient = $checkClient;
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return isset($this->checkClient->getContents()->active)
            && (bool)$this->checkClient->getContents()->active === true;
    }

    /**
     * [isNumber description]
     * @param  int $number [description]
     * @return bool           [description]
     */
    public function isNumber(int $number): bool
    {
        return isset($this->checkClient->getContents()->number)
            && (int)$this->checkClient->getContents()->number === $number;
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void
    {
        $this->checkClient->request([
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
