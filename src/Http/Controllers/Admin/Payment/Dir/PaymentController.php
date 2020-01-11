<?php

namespace N1ebieski\IDir\Http\Controllers\Admin\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\JsonResponse;
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
        $payments = $dir->payments()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => '',
            'view' => view('idir::admin.payment.show_logs', [
                'payments' => $payments,
            ])->render()
        ]);
    }
}
