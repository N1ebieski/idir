<?php

namespace N1ebieski\IDir\Services\Payment;

use Throwable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\ValueObjects\Payment\Status;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use Illuminate\Database\Eloquent\MassAssignmentException;
use N1ebieski\ICore\Services\Interfaces\StatusUpdateInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class PaymentService implements CreateInterface, StatusUpdateInterface
{
    /**
     * Model
     * @var Payment
     */
    protected $payment;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     *
     * @var DB
     */
    protected $db;

    /**
     *
     * @param Payment $payment
     * @param Carbon $carbon
     * @param Config $config
     * @param DB $db
     * @return void
     */
    public function __construct(Payment $payment, Carbon $carbon, Config $config, DB $db)
    {
        $this->payment = $payment;

        $this->carbon = $carbon;
        $this->config = $config;
        $this->db = $db;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            try {
                $this->payment->status = Status::fromString(
                    $attributes['payment_type'] ?? Status::UNFINISHED
                );
            } catch (\InvalidArgumentException $e) {
                $this->payment->status = Status::UNFINISHED;
            }

            $this->payment->driver = $this->config->get("idir.payment.{$attributes['payment_type']}.driver");

            $this->payment->morph()->associate($this->payment->morph);
            $this->payment->orderMorph()->associate($this->payment->order);

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
}
