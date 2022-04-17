<?php

namespace N1ebieski\IDir\Tests\Feature\Api\Payment\Dir;

use Tests\TestCase;
use Illuminate\Support\Str;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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

    public function testPaymentVerifyInactive()
    {
        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $response = $this->post(route('api.payment.dir.verify'), $this->providerSetup($payment->load('orderMorph')));

        $response->assertSeeText('OK');

        $this->assertDatabaseHas('payments', [
            'uuid' => $payment->uuid,
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Payment::UNFINISHED
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::INACTIVE
        ]);
    }

    public function testPaymentVerifyActive()
    {
        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $response = $this->post(route('api.payment.dir.verify'), $this->providerSetup($payment->load('orderMorph')));

        $response->assertSeeText('OK');

        $this->assertDatabaseHas('payments', [
            'uuid' => $payment->uuid,
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Payment::FINISHED
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::ACTIVE
        ]);
    }

    public function testPaymentVerifyStatusError()
    {
        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->providerSetup($payment->load('orderMorph'));
        $providerSetup['status'] = 'err';

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        $payment = $payment->find($payment->uuid);

        $this->assertTrue(Str::contains($payment->logs, 'Invalid status'));

        $response->assertOk();
    }

    public function testPaymentVerifyAmountError()
    {
        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->providerSetup($payment->load('orderMorph'));
        $providerSetup['amount'] = "999.99";

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        $payment = $payment->find($payment->uuid);

        $this->assertTrue(Str::contains($payment->logs, 'Invalid amount'));

        $response->assertOk();
    }

    public function testPaymentVerifySignError()
    {
        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->providerSetup($payment->load('orderMorph'));
        $providerSetup['sign'] = "dupa";

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        $payment = $payment->find($payment->uuid);

        $this->assertTrue(Str::contains($payment->logs, 'Invalid sign'));

        $response->assertOk();
    }
}
