<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\TransferClient;
use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\TransferUtilStrategy;

class TransferUtil implements TransferUtilStrategy
{
    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * [public description]
     * @var TransferClient
     */
    public $client;

    /**
     * Undocumented function
     *
     * @param TransferClient $client
     */
    public function __construct(Config $config, TransferClient $client)
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
        return isset($this->client->getContents()->status)
            && (string)$this->client->getContents()->status === "OK";
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void
    {
        $this->client->get($this->config->get('services.cashbill.code_transfer.check_url'), [
            'code' => $attributes['code'],
            'id' => $attributes['id']
        ]);

        if (!$this->isActive()) {
            throw new \N1ebieski\IDir\Exceptions\Payment\Cashbill\Codes\Transfer\InactiveCodeException(
                'Code is inactive.',
                403
            );
        }
    }
}
