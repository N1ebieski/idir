<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir\Polymorphic;

/**
 * [PaymentController description]
 */
class PaymentController implements Polymorphic
{
    /**
     * Display all the specified Payments logs for Dir.
     *
     * @param  Dir  $dir [description]
     * @return JsonResponse          [description]
     */
    public function showLogs(Dir $dir) : JsonResponse
    {
        return Response::json([
            'success' => '',
            'view' => View::make('idir::admin.payment.show_logs', [
                'payments' => $dir->makeRepo()->getPayments(),
            ])->render()
        ]);
    }
}
