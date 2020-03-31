<?php

namespace N1ebieski\IDir\Loads\Web\Payment;

use Illuminate\Http\Request;

class ShowLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('payment')->load(['morph', 'orderMorph']);
    }
}
