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

namespace N1ebieski\IDir\Tests\Feature\Web\Dir\Update;

use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Testing\Traits\Dir\HasDir;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Testing\Traits\Field\HasFields;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\ValueObjects\Field\Type as FieldType;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class Update3DirTest extends TestCase
{
    use HasDir;
    use HasFields;
    use DatabaseTransactions;

    public function testEdit3AsGuest(): void
    {
        $response = $this->get(route('web.dir.edit_3', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testEdit3NoExistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->get(route('web.dir.edit_3', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit3NoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->get(route('web.dir.edit_3', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit3Foreign(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testEdit3OldPaidGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->paidSeasonal()->withCategory()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.3')
            ->assertDontSee(Type::TRANSFER);
    }

    public function testEdit3NewPaidGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->paidSeasonal()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_3', [$dir->id, $newGroup->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.3')
            ->assertSee(Type::TRANSFER);
    }

    public function testUpdate3AsGuest(): void
    {
        $response = $this->put(route('web.dir.update_3', [34, 43]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdate3NoExistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_3', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdate3NoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->put(route('web.dir.update_3', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdate3Foreign(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdate3PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->private()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testUpdate3ValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredUrl()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testUpdate3ValidationCategoriesFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        /** @var Collection<Category> */
        $categories = Category::makeFactory()->count(3)->active()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertSessionHasErrors(['categories']);

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testUpdate3ValidationFieldsFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $fields = [];

        foreach (FieldType::getAvailable() as $type) {
            /** @var Field */
            $field = Field::makeFactory()->public()->hasAttached($newGroup, [], 'morphs')->{$type}()->create();

            $fields[] = "field.{$field->id}";
        }

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), []);

        $response->assertSessionHasErrors($fields);

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testUpdate3ValidationBacklinkFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        Link::makeFactory()->backlink()->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), []);

        $response->assertSessionHasErrors('backlink');

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testUpdate3ValidationBacklinkPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create([
            'url' => 'http://dadadad.pl'
        ]);

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($link) {
            $mock->shouldReceive('request')->once()
                ->with('GET', 'http://dadadad.pl/dasdas', ['verify' => false])
                ->andReturn(
                    new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">dadasdasd</a>')
                );
        });

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'backlink' => $link->id,
            'backlink_url' => 'http://dadadad.pl/dasdas'
        ]);

        $response->assertSessionDoesntHaveErrors('backlink_url');

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testUpdate3Fields(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(
            route('web.dir.update_3', [$dir->id, $newGroup->id]),
            ($setUpDir = $this->setUpDir()) + $this->setUpFields($newGroup)
        );

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('categories_models', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'category_id' => $setUpDir['categories'][0]
        ]);

        $this->assertDatabaseHas('tags_models', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
        ]);

        $this->assertDatabaseHas('fields_values', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass()
        ]);

        $response->assertRedirect(route('web.profile.dirs', [
            'filter' => [
                'search' => "id:\"{$dir->id}\""
            ]
        ]));
    }

    public function testUpdate3ValidationPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER
        ]);

        $response->assertSessionHasErrors('payment_transfer');

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testUpdate3ValidationNoExistPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => 23232
        ]);

        $response->assertSessionHasErrors('payment_transfer');

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testUpdate3NewGroupPayment(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => $price->id
        ] + $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::PAYMENT_INACTIVE
        ]);

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::PENDING
        ]);

        /** @var Payment */
        $payment = Payment::orderBy('created_at', 'desc')->first();

        $response->assertSessionDoesntHaveErrors('payment_transfer');

        $response->assertRedirect(route('web.payment.dir.show', [
            $payment->uuid,
            Config::get('idir.payment.transfer.driver')
        ]));
    }

    public function testUpdate3OldGroupWithoutPayment(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE
        ]);

        $this->assertDatabaseMissing('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::PENDING
        ]);

        $response->assertSessionDoesntHaveErrors('payment_transfer');

        $response->assertRedirect(route('web.profile.dirs', [
            'filter' => [
                'search' => "id:\"{$dir->id}\""
            ]
        ]));
    }

    public function testUpdate3PendingOldGroupPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->pending()->withCategory()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertSessionHasErrors('payment_type');

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testUpdate3ValidationPaymentAutoCodeSmsPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->seasonal()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
                // @phpstan-ignore-next-line
                new GuzzleResponse(HttpResponse::HTTP_OK, [], json_encode([
                    'active' => true,
                    'number' => (string)$price->number,
                    'activeFrom' => null,
                    'codeValidityTime' => 0,
                    'timeRemaining' => 0
                ]))
            );
        });

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => 'dsadasd7a8s'
        ] + $this->setUpDir());

        $dir->refresh();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::FINISHED
        ]);

        $this->assertTrue($dir->privileged_to !== null);

        $response->assertSessionDoesntHaveErrors('code_sms');

        $response->assertRedirect(route('web.profile.dirs', [
            'filter' => [
                'search' => "id:\"{$dir->id}\""
            ]
        ]));
    }
}
