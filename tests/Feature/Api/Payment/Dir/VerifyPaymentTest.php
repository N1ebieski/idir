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
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class VerifyPaymentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     *
     * @return array
     */
    // @phpstan-ignore-next-line
    private function providerProvider(): array
    {
        return [['cashbill']];
    }

    /**
     *
     * @param Payment $payment
     * @param string $provider
     * @return array
     * @throws BindingResolutionException
     */
    private function setupProvider(Payment $payment, string $provider): array
    {
        $key = Config::get('services.' . $provider . '.transfer.key');

        $setup['service'] = Config::get('services.' . $provider . '.transfer.service');
        $setup['orderid'] = '2372832783';
        $setup['amount'] = $payment->order->price;
        $setup['userdata'] = json_encode(['uuid' => $payment->uuid, 'redirect' => route('web.profile.dirs')]);
        $setup['status'] = 'ok';
        $setup['sign'] = md5($setup['service'] . $setup['orderid'] . $setup['amount']
        . $setup['userdata'] . $setup['status'] . $key);

        return $setup;
    }

    /**
     * @dataProvider providerProvider
     */
    public function testVerifyInactive(string $provider): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $response = $this->post(
            route('api.payment.dir.verify'),
            $this->setupProvider($payment->load('orderMorph'), $provider)
        );

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

    /**
     * @dataProvider providerProvider
     */
    public function testVerifyActive(string $provider): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $response = $this->post(
            route('api.payment.dir.verify'),
            $this->setupProvider($payment->load('orderMorph'), $provider)
        );

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

    /**
     * @dataProvider providerProvider
     */
    public function testVerifyStatusError(string $provider): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->setupProvider($payment->load('orderMorph'), $provider);
        $providerSetup['status'] = 'err';

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        /** @var Payment */
        $payment = $payment->find($payment->uuid);

        $this->assertTrue(is_string($payment->logs) && Str::contains($payment->logs, 'Invalid status'));

        $response->assertOk();
    }

    /**
     * @dataProvider providerProvider
     */
    public function testVerifyAmountError(string $provider): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->setupProvider($payment->load('orderMorph'), $provider);
        $providerSetup['amount'] = "999.99";

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        /** @var Payment */
        $payment = $payment->find($payment->uuid);

        $this->assertTrue(is_string($payment->logs) && Str::contains($payment->logs, 'Invalid amount'));

        $response->assertOk();
    }

    /**
     * @dataProvider providerProvider
     */
    public function testVerifySignError(string $provider): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->pending()->for($group)->create();

        /** @var Payment */
        $payment = Payment::makeFactory()->pending()->for($dir, 'morph')->for($price, 'orderMorph')->create();

        $providerSetup = $this->setupProvider($payment->load('orderMorph'), $provider);
        $providerSetup['sign'] = "dupa";

        $response = $this->post(route('api.payment.dir.verify'), $providerSetup);

        /** @var Payment */
        $payment = $payment->find($payment->uuid);

        $this->assertTrue(is_string($payment->logs) && Str::contains($payment->logs, 'Invalid sign'));

        $response->assertOk();
    }
}
