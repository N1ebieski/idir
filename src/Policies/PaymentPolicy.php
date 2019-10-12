<?php

namespace N1ebieski\IDir\Policies;

use N1ebieski\ICore\Models\User;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * [PaymentPolicy description]
 */
class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * [show description]
     * @param  User    $current_user [description]
     * @param  Payment $payment      [description]
     * @return bool                  [description]
     */
    public function show(User $current_user, Payment $payment) : bool
    {
        return $current_user->id === $payment->model->user_id;
    }
}
