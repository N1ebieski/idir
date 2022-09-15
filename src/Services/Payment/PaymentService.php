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

namespace N1ebieski\IDir\Services\Payment;

use Throwable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\ValueObjects\Payment\Status;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Database\Eloquent\MassAssignmentException;

class PaymentService
{
    /**
     *
     * @param Payment $payment
     * @param Carbon $carbon
     * @param Config $config
     * @param DB $db
     * @return void
     */
    public function __construct(
        protected Payment $payment,
        protected Carbon $carbon,
        protected Config $config,
        protected DB $db
    ) {
        //
    }

    /**
     *
     * @param array $attributes
     * @return Payment
     * @throws Throwable
     */
    public function create(array $attributes): Payment
    {
        return $this->db->transaction(function () use ($attributes) {
            try {
                $this->payment->status = Status::fromString(
                    $attributes['payment_type'] ?? Status::UNFINISHED
                );
            } catch (\InvalidArgumentException $e) {
                $this->payment->status = Status::unfinished();
            }

            $this->payment->driver = $this->config->get("idir.payment.{$attributes['payment_type']}.driver");

            $this->payment->morph()->associate($attributes['morph']);
            $this->payment->orderMorph()->associate($attributes['order']);

            $this->payment->save();

            return $this->payment;
        });
    }

    /**
     *
     * @param int $status
     * @return bool
     * @throws MassAssignmentException
     */
    public function updateStatus(int $status): bool
    {
        return $this->db->transaction(function () use ($status) {
            return $this->payment->update(['status' => $status]);
        });
    }

    /**
     *
     * @param string $logs
     * @return bool
     * @throws Throwable
     */
    public function updateLogs(string $logs): bool
    {
        return $this->db->transaction(function () use ($logs) {
            return $this->payment->update([
                'logs' => $this->payment->logs . "\r\n" . $this->carbon->now() . "\r\n" . $logs
            ]);
        });
    }

    /**
     * [completed description]
     * @return bool [description]
     */
    public function finished(): bool
    {
        return $this->db->transaction(function () {
            return $this->payment->update(['status' => Status::FINISHED]);
        });
    }

    /**
     * [paid description]
     * @return bool [description]
     */
    public function paid(): bool
    {
        return $this->db->transaction(function () {
            return $this->payment->update(['status' => Status::UNFINISHED]);
        });
    }
}
