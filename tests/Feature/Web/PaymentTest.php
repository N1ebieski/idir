<?php

namespace N1ebieski\IDir\Tests\Feature\Web;

use Illuminate\Support\Facades\Config;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Field\Group\Field;
use Tests\TestCase;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * [DirTest description]
 */
class PaymentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [PAYMENT_PROVIDER description]
     * @var string
     */
    const PAYMENT_PROVIDER = 'cashbill';

    /**
     * [protected description]
     * @var string
     */
    protected $key;

    /**
     * [setUp description]
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->key = Config::get('services.' . static::PAYMENT_PROVIDER . '.transfer.key');
    }

    /**
     * [providerSetup description]
     * @param  Payment $payment [description]
     * @return array            [description]
     */
    protected function providerSetup(Payment $payment) : array
    {
        $provider['service'] = config('services.' . static::PAYMENT_PROVIDER . '.transfer.service');
        $provider['orderid'] = '2372832783';
        $provider['amount'] = $payment->price_morph->price;
        $provider['userdata'] = $payment->id;
        $provider['status'] = 'ok';
        $provider['sign'] = md5($provider['service'].$provider['orderid'].$provider['amount']
        .$provider['userdata'].$provider['status'].$this->key);

        return $provider;
    }

    public function test_payment_verify_inactive()
    {
        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'pending'])->make();
        $dir->group()->associate($group)->save();

        $payment = factory(Payment::class)->states(['pending'])->make();
        $payment->morph()->associate($dir);
        $payment->price_morph()->associate($price);
        $payment->save();

        $response = $this->post(route('web.payment.dir.verify'), $this->providerSetup($payment));

        $response->assertSeeText('OK');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'price_id' => $price->id,
            'status' => 0
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 0
        ]);
    }

    public function test_payment_verify_active()
    {
        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'pending'])->make();
        $dir->group()->associate($group)->save();

        $payment = factory(Payment::class)->states(['pending'])->make();
        $payment->morph()->associate($dir);
        $payment->price_morph()->associate($price);
        $payment->save();

        $response = $this->post(route('web.payment.dir.verify'), $this->providerSetup($payment));

        $response->assertSeeText('OK');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'price_id' => $price->id,
            'status' => 1
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 1
        ]);
    }

    public function test_payment_verify_status_error()
    {
        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'pending'])->make();
        $dir->group()->associate($group)->save();

        $payment = factory(Payment::class)->states(['pending'])->make();
        $payment->morph()->associate($dir);
        $payment->price_morph()->associate($price);
        $payment->save();

        $providerSetup = $this->providerSetup($payment);
        $providerSetup['status'] = 'err';

        $response = $this->post(route('web.payment.dir.verify'), $providerSetup);

        $response->assertStatus(403)->assertSeeText('Invalid status');
    }

    public function test_payment_verify_amount_error()
    {
        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'pending'])->make();
        $dir->group()->associate($group)->save();

        $payment = factory(Payment::class)->states(['pending'])->make();
        $payment->morph()->associate($dir);
        $payment->price_morph()->associate($price);
        $payment->save();

        $providerSetup = $this->providerSetup($payment);
        $providerSetup['amount'] = "999.99";

        $response = $this->post(route('web.payment.dir.verify'), $providerSetup);

        $response->assertStatus(403)->assertSeeText('Invalid amount');
    }

    public function test_payment_verify_sign_error()
    {
        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'pending'])->make();
        $dir->group()->associate($group)->save();

        $payment = factory(Payment::class)->states(['pending'])->make();
        $payment->morph()->associate($dir);
        $payment->price_morph()->associate($price);
        $payment->save();

        $providerSetup = $this->providerSetup($payment);
        $providerSetup['sign'] = "dupa";

        $response = $this->post(route('web.payment.dir.verify'), $providerSetup);

        $response->assertStatus(403)->assertSeeText('Invalid sign');
    }
}
