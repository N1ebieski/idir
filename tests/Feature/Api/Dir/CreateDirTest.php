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

namespace N1ebieski\IDir\Tests\Feature\Api\Dir;

use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Testing\Traits\Dir\HasDir;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Testing\Traits\Field\HasFields;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron;
use N1ebieski\IDir\ValueObjects\Field\Type as FieldType;
use N1ebieski\IDir\ValueObjects\Price\Type as PriceType;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class CreateDirTest extends TestCase
{
    use HasDir;
    use HasFields;
    use DatabaseTransactions;

    public function testStoreNoExistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.dir.store', [rand(2, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testStorePrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->private()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStoreValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['title', 'categories', 'content_html', 'url']);
    }

    public function testStoreValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testStoreValidationCategoriesFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->maxCats()->create();

        /** @var Collection<Category> */
        $categories = Category::makeFactory()->count(3)->active()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['categories']);
    }

    public function testStoreValidationFieldsFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->maxCats()->create();

        $fields = [];

        foreach (FieldType::getAvailable() as $type) {
            /** @var Field*/
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

            $fields[] = "field.{$field->id}";
        }

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors($fields);
    }

    public function testStoreValidationBacklinkFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        Link::makeFactory()->backlink()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['backlink', 'backlink_url']);
    }

    public function testStoreAsGuestValidationEmailFail(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['email']);
    }

    public function testStoreInResource(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $response->assertJsonFragment([
            'title' => $this->setUpDir()['title'],
            'content_html' => "<p>{$this->setUpDir()['content_html']}</p>",
            'url' => $this->setUpDir()['url']
        ]);
    }

    public function testStoreValidationBacklinkPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        $backlinkUrl = 'https://idir.test/page-with-backlink';

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($link, $backlinkUrl) {
            $mock->shouldReceive('request')->once()->with('GET', $backlinkUrl, ['verify' => false])
                ->andReturn(
                    new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">backlink</a>')
                );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir() + [
            'backlink' => $link->id,
            'backlink_url' => $backlinkUrl
        ]);

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        /** @var Dir */
        $dir = Dir::first();

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('dirs_backlinks', [
            'dir_id' => $dir->id,
            'link_id' => $link->id,
            'url' => $backlinkUrl
        ]);
    }

    public function testStoreFieldsInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        Storage::fake('public');

        /** @var Group */
        $group = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        $setUpDir = $this->setUpDir();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $setUpDir + $this->setUpFields($group));

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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
    }

    public function testStoreValidationPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::TRANSFER
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testStoreValidationNoExistPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::TRANSFER,
            'payment_transfer' => rand(1, 1000)
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testStorePaymentInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        $setUpDir = $this->setUpDir();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::TRANSFER,
            'payment_transfer' => $price->id
        ] + $setUpDir);

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

        $response->assertJsonFragment(['uuid' => $payment->uuid]);
    }

    public function testStorePaymentAsGuestInDatabase(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        $setUpDir = $this->setUpDir();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'email' => 'kontakt@intelekt.net.pl',
            'payment_type' => PriceType::TRANSFER,
            'payment_transfer' => $price->id
        ] + $setUpDir);

        /** @var User */
        $user = User::orderBy('id', 'desc')->first();

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

        $response->assertJsonFragment(['uuid' => $payment->uuid]);
    }

    public function testStoreAsGuestInDatabase(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'email' => 'kontakt@intelekt.net.pl',
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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
    }

    public function testStoreValidationPaymentCodeSmsFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_SMS,
            'payment_code_sms' => $price->id
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['code_sms']);
    }

    public function testStoreValidationPaymentAutoCodeSmsPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

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

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => Str::random(10)
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::FINISHED
        ]);

        $this->assertTrue($dir->privileged_at !== null);
    }

    public function testStoreValidationPaymentAutoCodeSmsError(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

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

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => Str::random(10)
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['code_sms']);
    }

    public function testStoreValidationPaymentCodeTransferFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_TRANSFER,
            'payment_code_transfer' => $price->id
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['code_transfer']);
    }

    public function testStoreValidationPaymentAutoCodeTransferPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request')->once()->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], "OK\n23782738273")
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => Str::random(10)
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Status::INACTIVE
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status->isInactive());
    }

    public function testStoreValidationPaymentAutoCodeTransferError(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $this->mock(GuzzleClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request')->once()->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], "ERROR")
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => Str::random(10)
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['code_transfer']);
    }

    public function testStoreValidationPaymentLocalCodeTransferPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

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

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => $code->code
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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
            'status' => Status::INACTIVE
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status->isInactive());
    }

    public function testStoreValidationPaymentLocalCodeSmsPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

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

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => $code->code
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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
            'status' => Status::ACTIVE
        ]);

        $this->assertTrue($dir->privileged_at !== null && $dir->status->isActive());
    }

    public function testStoreModeratorNotificationDirs(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var User */
        $admin = User::makeFactory()->admin()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->withoutUrl()->applyInactive()->create();

        Config::set('idir.dir.notification.hours', 0);
        Config::set('idir.dir.notification.dirs', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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

    public function testStoreModeratorNotificationHours(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var User */
        $admin = User::makeFactory()->admin()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->withoutUrl()->applyInactive()->create();

        Config::set('idir.dir.notification.dirs', 0);
        Config::set('idir.dir.notification.hours', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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
