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

namespace N1ebieski\IDir\Exceptions;

use Exception;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class CustomException extends Exception
{
    /**
     * Undocumented function
     *
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            !empty($this->message) && empty($message) ? $this->message : $message,
            !empty($this->code) && empty($code) ? $this->code : $code,
            $previous
        );
    }

    /**
     * Report the exception.
     *
     * @return bool|void
     */
    public function report()
    {
        return false;
    }

    /**
     *
     * @param Request $request
     * @return bool|void
     */
    public function render(Request $request)
    {
        if (Config::get('app.debug') === true) {
            return false;
        }

        return App::abort($this->getCode(), $this->getMessage());
    }
}
