<?php

namespace N1ebieski\IDir\Loads\Api\Payment\Dir;

use Illuminate\Http\Request;

class ShowLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('payment_dir_pending')
            ->load(['morph', 'orderMorph']);

        $request->route('payment_dir_pending')->morph->loadAllRels();
    }
}
