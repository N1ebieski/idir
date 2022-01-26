<?php

namespace N1ebieski\IDir\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class CustomException extends Exception
{
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
