<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Clients\Payment\Cashbill\Transfer\Responses;

use N1ebieski\ICore\Http\Clients\Response;
use N1ebieski\IDir\Http\Clients\Payment\Interfaces\Transfer\Responses\CompleteResponseInterface;

class CompleteResponse extends Response implements CompleteResponseInterface
{
    /**
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->get('status') === "ok";
    }

    /**
     * [isService description]
     * @param  string $service [description]
     * @return bool            [description]
     */
    public function isService(string $service): bool
    {
        return $this->get('service') === $service;
    }

    /**
     *
     * @param string $amount
     * @return bool
     */
    public function isAmount(string $amount): bool
    {
        return number_format($this->get('amount'), 2, '.', '') === $amount;
    }

    /**
     *
     * @return bool
     */
    public function isSign(string $key): bool
    {
        return md5($this->get('service') . $this->get('orderid') . $this->get('amount')
            . $this->get('userdata') . $this->get('status') . $key) === $this->get('sign');
    }
}
