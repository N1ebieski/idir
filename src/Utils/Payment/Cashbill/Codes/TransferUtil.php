<?php

namespace N1ebieski\IDir\Utils\Payment\Cashbill\Codes;

use N1ebieski\IDir\Utils\Payment\Interfaces\Codes\TransferUtilStrategy;
use N1ebieski\IDir\Http\Clients\Payment\Cashbill\Codes\CheckTransferClient;

class TransferUtil implements TransferUtilStrategy
{
    /**
     * [public description]
     * @var CheckTransferClient
     */
    public $checkClient;

    /**
     * Undocumented function
     *
     * @param CheckTransferClient $checkClient
     */
    public function __construct(CheckTransferClient $checkClient)
    {
        $this->checkClient = $checkClient;
    }

    /**
     * [isActive description]
     * @return bool [description]
     */
    public function isActive(): bool
    {
        return isset($this->checkClient->getContents()->status)
            && (string)$this->checkClient->getContents()->status === "OK";
    }

    /**
     * [authorize description]
     * @param array $attributes [description]
     */
    public function authorize(array $attributes): void
    {
        $this->checkClient->request([
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
