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

namespace N1ebieski\IDir\Tests\Feature\Web\Dir\Create;

use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Tests\Feature\Traits\HasDir;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Tests\Feature\Traits\HasFields;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron;
use N1ebieski\IDir\ValueObjects\Field\Type as FieldType;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class DirTest extends TestCase
{
    use HasDir;
    use HasFields;
    use DatabaseTransactions;

    public function testDirCreate1(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Collection<Group>|array<Group> */
        $publicGroups = Group::makeFactory()->count(3)->public()->create();

        /** @var Group */
        $privateGroup = Group::makeFactory()->private()->create([
            'name' => 'Private Group'
        ]);

        Auth::login($user);

        $response = $this->get(route('web.dir.create_1'));

        $response->assertOk()
            ->assertViewIs('idir::web.dir.create.1')
            ->assertSee(route('web.dir.create_2', [$publicGroups[2]->id]))
            ->assertSee($publicGroups[2]->name)
            ->assertDontSee($privateGroup->name);
    }

    public function testDirCreate2NoexistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [34]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirCreate2PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->private()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirCreate2MaxModelsGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->maxModels()->create();

        Dir::makeFactory()->withUser()->for($group)->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirCreate2MaxModelsDailyGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->maxModelsDaily()->create();

        Dir::makeFactory()->withUser()->for($group)->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirCreate2(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->additionalOptionsForEditingContent()->create();

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertOk()
            ->assertViewIs('idir::web.dir.create.2')
            ->assertSee('trumbowyg')
            ->assertSee(route('web.dir.store_2', [$group->id]));
    }

    public function testDirStore2NoexistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.dir.store_2', [34]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirStore2PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->private()->create();

        Auth::login($user);

        $response = $this->post(route('web.dir.store_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirStore2ValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]));

        $response->assertSessionHasErrors(['categories', 'title', 'content', 'url']);
    }

    public function testDirStore2ValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testDirStore2(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]), $this->setUpDir());

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));

        $response->assertSessionHas('dir.title', $this->setUpDir()['title']);
    }

    public function testDirCreate3AsGuest(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $this->post(route('web.dir.store_2', [$group->id]), $this->setUpDir());

        $response2 = $this->get(route('web.dir.create_3', [$group->id]));

        $response2->assertSeeInOrder([Lang::get('idir::dirs.email.tooltip'), 'name="email"'], false);
    }

    public function testDirCreate3AsLogged(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $this->post(route('web.dir.store_2', [$group->id]), $this->setUpDir());

        $response2 = $this->get(route('web.dir.create_3', [$group->id]));

        $response2->assertDontSee(Lang::get('idir::dirs.email.tooltip'));
    }

    public function testDirStore3AsGuestValidationEmailFail(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), $this->setUpDir());

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationCategoriesFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->maxCats()->create();

        /** @var Collection<Category> */
        $categories = Category::makeFactory()->count(3)->active()->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertSessionHasErrors(['categories']);

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationFieldsFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->maxCats()->create();

        $fields = [];

        foreach (FieldType::getAvailable() as $type) {
            /** @var Field */
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

            $fields[] = "field.{$field->id}";
        }

        $response = $this->post(route('web.dir.store_3', [$group->id]), []);

        $response->assertSessionHasErrors($fields);

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationBacklinkFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        Link::makeFactory()->backlink()->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), []);

        $response->assertSessionHasErrors('backlink');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationBacklinkPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($link) {
            $mock->shouldReceive('request')->once()
                ->with('GET', 'http://dadadad.pl', ['verify' => false])
                ->andReturn(
                    new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">dadasdasd</a>')
                );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'backlink' => $link->id,
            'backlink_url' => 'http://dadadad.pl'
        ]);

        $response->assertSessionDoesntHaveErrors('backlink_url');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3Fields(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        Storage::fake('public');

        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        $response = $this->post(
            route('web.dir.store_3', [$group->id]),
            ($setUpDir = $this->setUpDir()) + $this->setUpFields($group)
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

        $response->assertRedirect(route('web.dir.show', [$dir->slug]));
    }

    public function testDirStore3ValidationPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        Price::makeFactory()->for($group)->transfer()->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::TRANSFER
        ]);

        $response->assertSessionHasErrors('payment_transfer');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationNoexistPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($group)->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => 23232
        ]);

        $response->assertSessionHasErrors('payment_transfer');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3Payment(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => $price->id
        ] + $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::PAYMENT_INACTIVE,
            'user_id' => $user->id
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

    public function testDirStore3AsGuest(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'email' => 'kontakt@intelekt.net.pl',
        ] + $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        /** @var User */
        $user = User::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE,
            'user_id' => $user->id
        ]);

        $this->assertTrue($user->email === 'kontakt@intelekt.net.pl');

        $response->assertRedirect(route('web.dir.create_1'));
    }

    public function testDirStore3ValidationPaymentCodeSmsFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_SMS,
            'payment_code_sms' => $price->id
        ] + $this->setUpDir());

        $response->assertSessionHasErrors('code_sms');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationPaymentAutoCodeSmsPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($price) {
            $mock->shouldReceive('request')->once()->andReturn(
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

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => 'dsadasd7a8s'
        ] + $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::FINISHED
        ]);

        $this->assertTrue($dir->privileged_at !== null);

        $response->assertSessionDoesntHaveErrors('code_sms');

        $response->assertRedirect(route('web.dir.show', [$dir->slug]));
    }

    public function testDirStore3ValidationPaymentAutoCodeSmsError(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request')->once()->andReturn(
                // @phpstan-ignore-next-line
                new GuzzleResponse(HttpResponse::HTTP_NOT_FOUND, [], json_encode([
                    'error' => 'Something wrong'
                ]))
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => 'dsadasd7a8s'
        ] + $this->setUpDir());

        $response->assertSessionHasErrors('code_sms');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationPaymentCodeTransferFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_TRANSFER,
            'payment_code_transfer' => $price->id
        ] + $this->setUpDir());

        $response->assertSessionHasErrors('code_transfer');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationPaymentAutoCodeTransferPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request')->once()->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], "OK\n23782738273")
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => 'dsadasd7a8s'
        ] + $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::UNFINISHED
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status->isInactive());

        $response->assertSessionDoesntHaveErrors('code_transfer');

        $response->assertRedirect(route('web.dir.create_1'));
    }

    public function testDirStore3ValidationPaymentAutoCodeTransferError(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request')->once()->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], "ERROR")
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => 'dsadasd7a8s'
        ] + $this->setUpDir());

        $response->assertSessionHasErrors('code_transfer');

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function testDirStore3ValidationPaymentLocalCodeTransferPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        /** @var Code */
        $code = Code::makeFactory()->one()->for($price)->create();

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => $code->code
        ] + $this->setUpDir());

        $this->assertDatabaseMissing('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::UNFINISHED
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status->isInactive());

        $response->assertSessionDoesntHaveErrors('code_transfer');

        $response->assertRedirect(route('web.dir.create_1'));
    }

    public function testDirStore3ValidationPaymentLocalCodeSmsPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->for($group)->create();

        /** @var Code */
        $code = Code::makeFactory()->two()->for($price)->create();

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => Type::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => $code->code
        ] + $this->setUpDir());

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity - 1
        ]);

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::FINISHED
        ]);

        $this->assertTrue($dir->privileged_at !== null && $dir->status->isActive());

        $response->assertSessionDoesntHaveErrors('code_sms');

        $response->assertRedirect(route('web.dir.show', [$dir->slug]));
    }

    public function testModeratorNotificationDirs(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var User */
        $admin = User::makeFactory()->admin()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->withoutUrl()->create();

        Config::set('idir.dir.notification.hours', 0);
        Config::set('idir.dir.notification.dirs', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $this->post(route('web.dir.store_3', [$group->id]), $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE,
            'group_id' => $group->id,
            'privileged_to' => null
        ]);

        Mail::assertQueued(ModeratorMail::class, function (ModeratorMail $mail) use ($admin) {
            $mail->build(
                App::make(Dir::class),
                App::make(\Illuminate\Contracts\Translation\Translator::class)
            );

            return $mail->hasTo($admin->email);
        });
    }

    public function testModeratorNotificationHours(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var User */
        $admin = User::makeFactory()->admin()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->withoutUrl()->create();

        Config::set('idir.dir.notification.dirs', 0);
        Config::set('idir.dir.notification.hours', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $this->post(route('web.dir.store_3', [$group->id]), $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE,
            'group_id' => $group->id,
            'privileged_to' => null
        ]);

        $schedule = App::make(ModeratorNotificationCron::class);
        $schedule();

        Mail::assertQueued(ModeratorMail::class, function (ModeratorMail $mail) use ($admin) {
            $mail->build(
                App::make(Dir::class),
                App::make(\Illuminate\Contracts\Translation\Translator::class)
            );

            return $mail->hasTo($admin->email);
        });
    }
}
