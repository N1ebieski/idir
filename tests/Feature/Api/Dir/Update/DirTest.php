<?php

namespace N1ebieski\IDir\Tests\Feature\Api\Dir\Update;

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Client as GuzzleClient;
use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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

    public function testApiDirUpdateAsGuest()
    {
        $response = $this->putJson(route('api.dir.update', [rand(1, 1000), Group::DEFAULT]));

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testApiDirUpdateAsUserWithoutPermission()
    {
        $user = factory(User::class)->states('user')->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group);
        $dir->user()->associate($user);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertJson(['message' => 'User does not have the right permissions.']);
    }

    public function testApiDirUpdateAsUserWithoutAbility()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, []);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group);
        $dir->user()->associate($user);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertJson(['message' => 'Invalid ability provided.']);
    }

    public function testApiDirUpdateForeignDir()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->states('with_user')->make();
        $dir->group()->associate($group)->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $dir->group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateNoExistDir()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $response = $this->putJson(route('api.dir.update', [rand(1, 1000), Group::DEFAULT]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirUpdateNoExistGroup()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, rand(2, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirUpdatePrivateGroup()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $privateGroup = factory(Group::class)->states('private')->create();

        $publicGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($publicGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $privateGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateMaxModelsNewGroup()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'max_models'])->create();

        $dirInNewGroup = factory(Dir::class)->states(['with_user'])
            ->create(['group_id' => $newGroup->id]);

        $oldGroup = factory(Group::class)->states('public')->create();

        $dirInOldGroup = factory(Dir::class)->make();
        $dirInOldGroup->group()->associate($oldGroup->id);
        $dirInOldGroup->user()->associate($user->id)->save();

        $response = $this->putJson(route('api.dir.update', [$dirInOldGroup->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateMaxModelsOldGroup()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public', 'max_models'])->create();

        $dir = factory(Dir::class)->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_OK);
    }

    public function testApiDirUpdateValidationFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'required_url'])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'title' => '',
            'categories' => [],
            'content_html' => '',
            'url' => ''
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['title', 'categories', 'content_html', 'url']);
    }

    public function testApiDirUpdateValidationUrlFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'required_url'])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testApiDirUpdateValidationCategoriesFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'max_cats'])->create();

        $categories = factory(Category::class, 3)->states('active')->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['categories']);
    }

    public function testApiDirUpdateValidationFieldsFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'max_cats'])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        foreach (static::FIELD_TYPES as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($newGroup);

            $fields[] = "field.{$field->id}";
        }

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($fields);
    }

    public function testApiDirUpdateValidationBacklinkFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->make();
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $link = factory(Link::class)->states('backlink')->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['backlink', 'backlink_url']);
    }

    public function testApiDirUpdateInResource()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)
            ->states([
                'public',
                'required_url',
                'additional_options_for_editing_content'
            ])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonFragment([
            'title' => $this->dirSetup()['title'],
            'content_html' => "<p>{$this->dirSetup()['content_html']}</p>",
            'url' => $this->dirSetup()['url']
        ]);
    }

    public function testApiDirUpdateUserAsUserInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public', 'without_url'])->create();

        $newUser = factory(User::class)->states('user')->create();

        $dir = factory(Dir::class)->states('without_url', 'with_category')->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), [
            'user' => $newUser->id
        ]);

        $response->assertStatus(HttpResponse::HTTP_OK);
        $this->assertDatabaseMissing('dirs', [
            'id' => $dir->id,
            'user_id' => $newUser->id
        ]);
    }

    public function testApiDirUpdateUserAsAdminInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api', 'admin'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public', 'without_url'])->create();

        $newUser = factory(User::class)->states('user')->create();

        $dir = factory(Dir::class)->states('without_url', 'with_category')->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), [
            'user' => $newUser->id
        ]);

        $response->assertStatus(HttpResponse::HTTP_OK);
        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'user_id' => $newUser->id
        ]);
    }

    public function testApiDirUpdateBacklinkInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->make(['url' => 'https://idir.test']);
        $dir->group()->associate($oldGroup->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $link = factory(Link::class)->states('backlink')->create();

        $backlinkUrl = 'https://idir.test/page-with-backlink';

        $this->mock(GuzzleClient::class, function ($mock) use ($link, $backlinkUrl) {
            $mock->shouldReceive('request')->with('GET', $backlinkUrl, ['verify' => false])->andReturn(
                new GuzzleResponse(200, [], '<a href="' . $link->url . '">backlink</a>')
            );
        });

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'backlink' => $link->id,
            'backlink_url' => $backlinkUrl
        ]);

        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertStatus(HttpResponse::HTTP_OK);
        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('dirs_backlinks', [
            'dir_id' => $dir->id,
            'link_id' => $link->id,
            'url' => $backlinkUrl
        ]);
    }

    public function testApiDirUpdateExistBacklinkInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $dir = factory(Dir::class)->make(['url' => 'https://idir.test']);
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id);
        $dir->save();

        $link = factory(Link::class)->states('backlink')->create();

        $backlink = DirBacklink::make(['url' => 'https://idir.test/page-with-backlink']);
        $backlink->dir()->associate($dir->id);
        $backlink->link()->associate($link->id);
        $backlink->save();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertDatabaseHas('dirs', [
            'title' => $this->dirSetup()['title']
        ]);
    }

    public function testApiDirUpdateFieldsInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'apply_active', 'required_url'])->create();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $oldGroup->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(
            route('api.dir.update', [$dir->id, $newGroup->id]),
            ($dirSetup = $this->dirSetup()) + $this->fieldsSetup($newGroup)
        );

        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertStatus(HttpResponse::HTTP_OK);
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

    public function testApiDirUpdateExistFieldsInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        foreach (['input', 'textarea'] as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($group);

            $dir->fields()->attach([
                $field->id => ['value' => json_encode('Commodo laborum irure mollit laborum occaecat adipisicing dolore.')]
            ]);
        }

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->dirSetup());

        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertStatus(HttpResponse::HTTP_OK);
        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('fields_values', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass()
        ]);
    }

    public function testApiDirUpdateValidationPaymentFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($newGroup);
        $price->save();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $oldGroup->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => 'transfer'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirUpdateValidationNoExistPaymentFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($newGroup);
        $price->save();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $oldGroup->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => rand(1, 1000)
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirUpdateNewGroupPaymentInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($newGroup)->save();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $oldGroup->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => $price->id
        ] + $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::PAYMENT_INACTIVE
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

    public function testApiDirUpdateOldGroupWithoutPaymentInDatabase()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'title' => $this->dirSetup()['title'],
            'status' => Dir::INACTIVE
        ]);

        $this->assertDatabaseMissing('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Payment::PENDING
        ]);
    }

    public function testApiDirUpdatePendingValidationPaymentFail()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['pending', 'with_category'])->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->dirSetup());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_type']);
    }

    public function testApiDirUpdateValidationPaymentAutoCodeSmsPass()
    {
        $user = factory(User::class)->states(['user', 'api'])->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms', 'seasonal'])->make();
        $price->group()->associate($newGroup)->save();

        $oldGroup = factory(Group::class)->states('public')->create();

        $dir = factory(Dir::class)->states(['with_category'])->create([
            'group_id' => $oldGroup->id,
            'user_id' => $user->id
        ]);

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

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => Str::random(6)
        ] + $this->dirSetup());

        $dir->refresh();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Dir::ACTIVE
        ]);

        $response->assertStatus(HttpResponse::HTTP_OK);
        $this->assertTrue($dir->privileged_to !== null);
    }
}
