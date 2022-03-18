<?php

namespace N1ebieski\IDir\Tests\Feature\Api\Dir\Create;

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\User;
use N1ebieski\ICore\Models\Link;
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
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Crons\Dir\ModeratorNotificationCron;

class DirTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [FIELD_TYPES description]
     * @var array
     */
    private const FIELD_TYPES = ['input', 'textarea', 'select', 'multiselect', 'checkbox', 'image'];

    /**
     * [dirSetup description]
     * @return array [description]
     */
    protected function dirSetup(): array
    {
        $category = factory(Category::class)->states('active')->create();

        return [
            'title' => 'Dolore deserunt et ex cupidatat.',
            'tags' => ['cupidatat', 'nulla quis', 'magna'],
            'content_html' => 'Aute ipsum laboris ullamco incididunt amet mollit reprehenderit est duis est. Qui fugiat id eu ex eu ex. Magna enim ipsum amet excepteur excepteur qui ad commodo laborum labore velit Lorem sint. Ad nisi dolore commodo non Lorem duis sint quis. Eiusmod sunt eiusmod est deserunt eiusmod reprehenderit est tempor commodo laboris.',
            'categories' => [$category->id],
            'url' => 'https://idir.test'
        ];
    }

    /**
     * [fieldsSetup description]
     * @param  Group $group [description]
     * @return array        [description]
     */
    protected function fieldsSetup(Group $group): array
    {
        foreach (static::FIELD_TYPES as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($group);

            $key = $field->id;

            if (in_array($field->type, ['input', 'textarea'])) {
                $fields['field'][$key] = 'Cupidatat magna enim officia non sunt esse qui Lorem quis.';
            } elseif ($field->type === 'select') {
                $fields['field'][$key] = $field->options->options[0];
            } elseif (in_array($field->type, ['multiselect', 'checkbox'])) {
                $fields['field'][$key] = array_slice($field->options->options, 0, 2);
            } elseif ($field->type === 'image') {
                $fields['field'][$key] = UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000);
            }
        }

        return $fields;
    }

    public function testApiDirStoreNoExistGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $response = $this->postJson(route('api.dir.store', [rand(2, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirStorePrivateGroup()
    {
        $user = factory(User::class)->states('user')->create();

        $group = factory(Group::class)->states('private')->create();

        Sanctum::actingAs($user, []);

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirStoreValidationFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['title', 'categories', 'content_html', 'url']);
    }

    public function testApiDirStoreValidationUrlFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testApiDirStoreValidationCategoriesFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'max_cats'])->create();

        $categories = factory(Category::class, 3)->states('active')->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['categories']);
    }

    public function testApiDirStoreValidationFieldsFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'max_cats'])->create();

        foreach (static::FIELD_TYPES as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($group);

            $fields[] = "field.{$field->id}";
        }

        $response = $this->postJson(route('api.dir.store', [$group->id]), []);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($fields);
    }

    public function testApiDirStoreValidationBacklinkFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $link = factory(Link::class)->states('backlink')->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), []);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['backlink', 'backlink_url']);
    }

    public function testApiDirStoreAsGuestValidationEmailFail()
    {
        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testApiDirStoreInResource()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)
            ->states([
                'public',
                'required_url',
                'additional_options_for_editing_content'
            ])->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_CREATED);
        $response->assertJsonFragment([
            'title' => $this->dirSetup()['title'],
            'content_html' => "<p>{$this->dirSetup()['content_html']}</p>",
            'url' => $this->dirSetup()['url']
        ]);
    }

    public function testApiDirStoreValidationBacklinkPass()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $link = factory(Link::class)->states('backlink')->create();

        $backlinkUrl = 'https://idir.test/page-with-backlink';

        $this->mock(GuzzleClient::class, function ($mock) use ($link, $backlinkUrl) {
            $mock->shouldReceive('request')->with('GET', $backlinkUrl, ['verify' => false])->andReturn(
                new GuzzleResponse(200, [], '<a href="' . $link->url . '">backlink</a>')
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->dirSetup() + [
            'backlink' => $link->id,
            'backlink_url' => $backlinkUrl
        ]);

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::make()->where('url', $this->dirSetup()['url'])->first();

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('dirs_backlinks', [
            'dir_id' => $dir->id,
            'link_id' => $link->id,
            'url' => $backlinkUrl
        ]);
    }

    public function testApiDirStoreFieldsInDatabase()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        Storage::fake('public');

        $group = factory(Group::class)->states(['public', 'apply_active', 'required_url'])->create();

        $dirSetup = $this->dirSetup();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $dirSetup + $this->fieldsSetup($group));

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('categories_models', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'category_id' => $dirSetup['categories'][0]
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
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'transfer'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirStoreValidationNoExistPaymentFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => rand(1, 1000)
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirStorePaymentInDatabase()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $dirSetup = $this->dirSetup();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => $price->id
        ] + $dirSetup);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::PAYMENT_INACTIVE,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Payment::PENDING
        ]);

        $payment = Payment::orderBy('created_at', 'desc')->first();

        $response->assertJsonFragment(['uuid' => $payment->uuid]);
    }

    public function testApiDirStoreAsGuestInDatabase()
    {
        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'email' => 'kontakt@intelekt.net.pl',
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $user = User::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::INACTIVE,
            'user_id' => $user->id
        ]);

        $this->assertTrue($user->email === 'kontakt@intelekt.net.pl');
    }

    public function testApiDirStoreValidationPaymentCodeSmsFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group);
        $price->save();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['code_sms']);
    }

    public function testApiDirStoreValidationPaymentAutoCodeSmsPass()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group);
        $price->save();

        $this->mock(GuzzleClient::class, function ($mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(200, [], json_encode([
                    'active' => true,
                    'number' => $price->number,
                    'activeFrom' => null,
                    'codeValidityTime' => 0,
                    'timeRemaining' => 0
                ]))
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => Str::random(10)
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Payment::FINISHED
        ]);

        $this->assertTrue($dir->privileged_at !== null);
    }

    public function testApiDirStoreValidationPaymentAutoCodeSmsError()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group);
        $price->save();

        $this->mock(GuzzleClient::class, function ($mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(404, [], json_encode([
                    'error' => 'Something wrong'
                ]))
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => Str::random(10)
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['code_sms']);
    }

    public function testApiDirStoreValidationPaymentCodeTransferFail()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['code_transfer']);
    }

    public function testApiDirStoreValidationPaymentAutoCodeTransferPass()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $this->mock(GuzzleClient::class, function ($mock) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(200, [], "OK\n23782738273")
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id,
            'code_transfer' => Str::random(10)
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Dir::INACTIVE
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status === Dir::INACTIVE);
    }

    public function testApiDirStoreValidationPaymentAutoCodeTransferError()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $this->mock(GuzzleClient::class, function ($mock) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(200, [], "ERROR")
            );
        });

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id,
            'code_transfer' => Str::random(10)
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['code_transfer']);
    }

    public function testApiDirStoreValidationPaymentLocalCodeTransferPass()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group);
        $price->save();

        $code = factory(Code::class)->states(['one'])->make();
        $code->price()->associate($price);
        $code->save();

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id,
            'code_transfer' => $code->code
        ] + $this->dirSetup());

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
            'status' => Dir::INACTIVE
        ]);

        $this->assertTrue($dir->privileged_at === null && $dir->status === Dir::INACTIVE);
    }

    public function testApiDirStoreValidationPaymentLocalCodeSmsPass()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group);
        $price->save();

        $code = factory(Code::class)->states(['two'])->make();
        $code->price()->associate($price);
        $code->save();

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $response = $this->postJson(route('api.dir.store', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => $code->code
        ] + $this->dirSetup());

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
            'status' => Dir::ACTIVE
        ]);

        $this->assertTrue($dir->privileged_at !== null && $dir->status === Dir::ACTIVE);
    }

    public function testApiDirStoreModeratorNotificationDirs()
    {
        $user = factory(User::class)->states('user')->create();

        $admin = factory(User::class)->states('admin')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_inactive', 'without_url'])->create();

        Config::set('idir.dir.notification.hours', 0);
        Config::set('idir.dir.notification.dirs', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::INACTIVE,
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
        $user = factory(User::class)->states('user')->create();

        $admin = factory(User::class)->states('admin')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public', 'apply_inactive', 'without_url'])->create();

        Config::set('idir.dir.notification.dirs', 0);
        Config::set('idir.dir.notification.hours', 1);

        Artisan::call('cache:clear system');

        Mail::fake();

        $response = $this->postJson(route('api.dir.store', [$group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_CREATED);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::INACTIVE,
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
