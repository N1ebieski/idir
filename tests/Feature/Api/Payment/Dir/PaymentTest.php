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

namespace N1ebieski\IDir\Tests\Feature\Api\Payment\Dir;

use Tests\TestCase;
use Illuminate\Support\Str;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class PaymentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [PAYMENT_PROVIDER description]
     * @var string
     */
    private const PAYMENT_PROVIDER = 'cashbill';

    /**
     * [providerSetup description]
     * @param  Payment $payment [description]
     * @return array            [description]
     */
    protected function providerSetup(Payment $payment): array
    {
        $key = Config::get('services.' . self::PAYMENT_PROVIDER . '.transfer.key');

        $provider['service'] = Config::get('services.' . self::PAYMENT_PROVIDER . '.transfer.service');
        $provider['orderid'] = '2372832783';
        $provider['amount'] = $payment->order->price;
        $provider['userdata'] = json_encode(['uuid' => $payment->uuid, 'redirect' => route('web.profile.dirs')]);
        $provider['status'] = 'ok';
        $provider['sign'] = md5($provider['service'] . $provider['orderid'] . $provider['amount']
        . $provider['userdata'] . $provider['status'] . $key);

        return $provider;
    }

    public function testPaymentVerifyInactive(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $response = $this->post(route('api.payment.dir.verify'), $this->providerSetup($payment->load('orderMorph')));

        dd($response->getContent());
        $response->assertSeeText('OK');

        $this->assertDatabaseHas('payments', [
            'uuid' => $payment->uuid,
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::UNFINISHED
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE
        ]);
    }

    public function testPaymentVerifyActive(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $response = $this->post(route('api.payment.dir.verify'), $this->providerSetup($payment->load('orderMorph')));

        $response->assertSeeText('OK');

        $this->assertDatabaseHas('payments', [
            'uuid' => $payment->uuid,
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::FINISHED
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);
    }

    public function testPaymentVerifyStatusError(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->providerSetup($payment->load('orderMorph'));
        $providerSetup['status'] = 'err';

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        /** @var Payment */
        $payment = $payment->find($payment->uuid);

        $this->assertTrue(is_string($payment->logs) && Str::contains($payment->logs, 'Invalid status'));

        $response->assertOk();
    }

    public function testPaymentVerifyAmountError(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->providerSetup($payment->load('orderMorph'));
        $providerSetup['amount'] = "999.99";

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        /** @var Payment */
        $payment = $payment->find($payment->uuid);

        $this->assertTrue(is_string($payment->logs) && Str::contains($payment->logs, 'Invalid amount'));

        $response->assertOk();
    }

    public function testPaymentVerifySignError(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->providerSetup($payment->load('orderMorph'));
        $providerSetup['sign'] = "dupa";

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        /** @var Payment */
        $payment = $payment->find($payment->uuid);

        $this->assertTrue(is_string($payment->logs) && Str::contains($payment->logs, 'Invalid sign'));

        $response->assertOk();
    }
}
