<?php

namespace N1ebieski\IDir\Tests\Feature\Api\Dir\Create;

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use N1ebieski\IDir\Mail\Dir\ModeratorMail;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron;
use N1ebieski\IDir\ValueObjects\Field\Type as FieldType;
use N1ebieski\IDir\ValueObjects\Price\Type as PriceType;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class DirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [setUpDir description]
     * @return array [description]
     */
    protected function setUpDir(): array
    {
        $category = Category::makeFactory()->active()->create();

        return [
            'title' => 'Dolore deserunt et ex cupidatat.',
            'tags' => ['cupidatat', 'nulla quis', 'magna'],
            'content_html' => 'Aute ipsum laboris ullamco incididunt amet mollit reprehenderit est duis est. Qui fugiat id eu ex eu ex. Magna enim ipsum amet excepteur excepteur qui ad commodo laborum labore velit Lorem sint. Ad nisi dolore commodo non Lorem duis sint quis. Eiusmod sunt eiusmod est deserunt eiusmod reprehenderit est tempor commodo laboris.',
            'categories' => [$category->id],
            'url' => 'https://idir.test'
        ];
    }

    /**
     * [setUpFields description]
     * @param  Group $group [description]
     * @return array        [description]
     */
    protected function setUpFields(Group $group): array
    {
        foreach (FieldType::getAvailable() as $type) {
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

            switch ($field->type) {
                case FieldType::INPUT:
                case FieldType::TEXTAREA:
                    $fields['field'][$field->id] = 'Cupidatat magna enim officia non sunt esse qui Lorem quis.';
                    break;

                case FieldType::SELECT:
                    $fields['field'][$field->id] = $field->options->options[0];
                    break;

                case FieldType::MULTISELECT:
                case FieldType::CHECKBOX:
                    $fields['field'][$field->id] = array_slice($field->options->options, 0, 2);
                    break;

                case FieldType::IMAGE:
                    $fields['field'][$field->id] = UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000);
                    break;
            }
        }

        return $fields;
    }

    public function testApiDirStoreNoExistGroup()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.dir.store', [rand(2, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirStorePrivateGroup()
    {
        $user = User::makeFactory()->user()->create();

        $group = Group::makeFactory()->private()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirStoreValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['title', 'categories', 'content_html', 'url']);
    }

    public function testApiDirStoreValidationUrlFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testApiDirStoreValidationCategoriesFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->maxCats()->create();

        $categories = Category::makeFactory()->count(3)->active()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['categories']);
    }

    public function testApiDirStoreValidationFieldsFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->maxCats()->create();

        foreach (FieldType::getAvailable() as $type) {
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

            $fields[] = "field.{$field->id}";
        }

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($fields);
    }

    public function testApiDirStoreValidationBacklinkFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        $link = Link::makeFactory()->backlink()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['backlink', 'backlink_url']);
    }

    public function testApiDirStoreAsGuestValidationEmailFail()
    {
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testApiDirStoreInResource()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);
        $response->assertJsonFragment([
            'title' => $this->setUpDir()['title'],
            'content_html' => "<p>{$this->setUpDir()['content_html']}</p>",
            'url' => $this->setUpDir()['url']
        ]);
    }

    public function testApiDirStoreValidationBacklinkPass()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        $link = Link::makeFactory()->backlink()->create();

        $backlinkUrl = 'https://idir.test/page-with-backlink';

        $this->mock(GuzzleClient::class, function ($mock) use ($link, $backlinkUrl) {
            $mock->shouldReceive('request')->with('GET', $backlinkUrl, ['verify' => false])->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">backlink</a>')
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir() + [
            'backlink' => $link->id,
            'backlink_url' => $backlinkUrl
        ]);

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::first();

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('dirs_backlinks', [
            'dir_id' => $dir->id,
            'link_id' => $link->id,
            'url' => $backlinkUrl
        ]);
    }

    public function testApiDirStoreFieldsInDatabase()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        Storage::fake('public');

        $group = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        $setUpDir = $this->setUpDir();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $setUpDir + $this->setUpFields($group));

        $response->assertStatus(HttpResponse::HTTP_CREATED);

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

    public function testApiDirStoreValidationPaymentFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::TRANSFER
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirStoreValidationNoExistPaymentFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::TRANSFER,
            'payment_transfer' => rand(1, 1000)
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirStorePaymentInDatabase()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $setUpDir = $this->setUpDir();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::TRANSFER,
            'payment_transfer' => $price->id
        ] + $setUpDir);

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

        $payment = Payment::orderBy('created_at', 'desc')->first();

        $response->assertJsonFragment(['uuid' => $payment->uuid]);
    }

    public function testApiDirStoreAsGuestInDatabase()
    {
        $group = Group::makeFactory()->public()->applyInactive()->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'email' => 'kontakt@intelekt.net.pl',
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $user = User::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE,
            'user_id' => $user->id
        ]);

        $this->assertTrue($user->email === 'kontakt@intelekt.net.pl');
    }

    public function testApiDirStoreValidationPaymentCodeSmsFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_SMS,
            'payment_code_sms' => $price->id
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['code_sms']);
    }

    public function testApiDirStoreValidationPaymentAutoCodeSmsPass()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $this->mock(GuzzleClient::class, function ($mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], json_encode([
                    'active' => true,
                    'number' => $price->number,
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

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => PaymentStatus::FINISHED
        ]);

        $this->assertTrue($dir->privileged_at !== null);
    }

    public function testApiDirStoreValidationPaymentAutoCodeSmsError()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->codeSms()->for($group)->create();

        $this->mock(GuzzleClient::class, function ($mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
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

    public function testApiDirStoreValidationPaymentCodeTransferFail()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_TRANSFER,
            'payment_code_transfer' => $price->id
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['code_transfer']);
    }

    public function testApiDirStoreValidationPaymentAutoCodeTransferPass()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $this->mock(GuzzleClient::class, function ($mock) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], "OK\n23782738273")
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => PriceType::CODE_TRANSFER,
            'payment_code_transfer' => $price->id,
            'code_transfer' => Str::random(10)
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Status::INACTIVE
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status->isInactive());
    }

    public function testApiDirStoreValidationPaymentAutoCodeTransferError()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

        $this->mock(GuzzleClient::class, function ($mock) {
            $mock->shouldReceive('request')->andReturn(
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

    public function testApiDirStoreValidationPaymentLocalCodeTransferPass()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->codeTransfer()->for($group)->create();

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

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Status::INACTIVE
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status->isInactive());
    }

    public function testApiDirStoreValidationPaymentLocalCodeSmsPass()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->codeSms()->for($group)->create();

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

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Status::ACTIVE
        ]);

        $this->assertTrue($dir->privileged_at !== null && $dir->status->isActive());
    }

    public function testApiDirStoreModeratorNotificationDirs()
    {
        $user = User::makeFactory()->user()->create();

        $admin = User::makeFactory()->admin()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->withoutUrl()->applyInactive()->create();

        Config::set('idir.dir.notification.hours', 0);
        Config::set('idir.dir.notification.dirs', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE,
            'group_id' => $group->id,
            'privileged_to' => null
        ]);

        Mail::assertQueued(ModeratorMail::class, function ($mail) use ($admin) {
            $mail->build(
                App::make(Dir::class),
                App::make(\Illuminate\Contracts\Translation\Translator::class)
            );

            return $mail->hasTo($admin->email);
        });
    }

    public function testApiDirStoreModeratorNotificationHours()
    {
        $user = User::makeFactory()->user()->create();

        $admin = User::makeFactory()->admin()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->withoutUrl()->applyInactive()->create();

        Config::set('idir.dir.notification.dirs', 0);
        Config::set('idir.dir.notification.hours', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::INACTIVE,
            'group_id' => $group->id,
            'privileged_to' => null
        ]);

        $schedule = App::make(ModeratorNotificationCron::class);
        $schedule();

        Mail::assertQueued(ModeratorMail::class, function ($mail) use ($admin) {
            $mail->build(
                App::make(Dir::class),
                App::make(\Illuminate\Contracts\Translation\Translator::class)
            );

            return $mail->hasTo($admin->email);
        });
    }
}