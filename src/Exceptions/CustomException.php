<?php

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
     * @return void
     */
    public function report()
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function render(Request $request)
    {
        if (Config::get('app.debug') === true) {
            return false;
        }

        return App::abort($this->getCode(), $this->getMessage());
    }
}
