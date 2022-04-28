<?php

namespace N1ebieski\IDir\Tests\Feature\Api\Dir\Update;

use Tests\TestCase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Client as GuzzleClient;
use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;
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
        foreach (static::FIELD_TYPES as $type) {
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

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
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertJson(['message' => 'User does not have the right permissions.']);
    }

    public function testApiDirUpdateAsUserWithoutAbility()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertJson(['message' => 'Invalid ability provided.']);
    }

    public function testApiDirUpdateForeignDir()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withUser()->for($group)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $dir->group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateNoExistDir()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $response = $this->putJson(route('api.dir.update', [rand(1, 1000), Group::DEFAULT]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirUpdateNoExistGroup()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, rand(2, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirUpdatePrivateGroup()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $privateGroup = Group::makeFactory()->private()->create();

        $publicGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($publicGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $privateGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateMaxModelsNewGroup()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->maxModels()->create();

        $dirInNewGroup = Dir::makeFactory()->withUser()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dirInOldGroup = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dirInOldGroup->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateMaxModelsOldGroup()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->maxModels()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_OK);
    }

    public function testApiDirUpdateValidationFail()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->requiredUrl()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

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
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->requiredUrl()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['url']);
    }

    public function testApiDirUpdateValidationCategoriesFail()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        $categories = Category::makeFactory()->count(3)->active()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['categories']);
    }

    public function testApiDirUpdateValidationFieldsFail()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        foreach (static::FIELD_TYPES as $type) {
            $field = Field::makeFactory()->public()->hasAttached($newGroup, [], 'morphs')->{$type}()->create();

            $fields[] = "field.{$field->id}";
        }

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($fields);
    }

    public function testApiDirUpdateValidationBacklinkFail()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $link = Link::makeFactory()->backlink()->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['backlink', 'backlink_url']);
    }

    public function testApiDirUpdateInResource()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);
        $response->assertJsonFragment([
            'title' => $this->setUpDir()['title'],
            'content_html' => "<p>{$this->setUpDir()['content_html']}</p>",
            'url' => $this->setUpDir()['url']
        ]);
    }

    public function testApiDirUpdateUserAsUserInDatabase()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->withoutUrl()->create();

        $newUser = User::makeFactory()->user()->create();

        $dir = Dir::makeFactory()->withoutUrl()->withCategory()->for($group)->for($user)->create();

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
        $user = User::makeFactory()->user()->api()->admin()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->withoutUrl()->create();

        $newUser = User::makeFactory()->user()->create();

        $dir = Dir::makeFactory()->withoutUrl()->withCategory()->for($group)->for($user)->create();

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
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create([
            'url' => 'https://idir.test'
        ]);

        $link = Link::makeFactory()->backlink()->create();

        $backlinkUrl = 'https://idir.test/page-with-backlink';

        $this->mock(GuzzleClient::class, function ($mock) use ($link, $backlinkUrl) {
            $mock->shouldReceive('request')->with('GET', $backlinkUrl, ['verify' => false])->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">backlink</a>')
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
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create([
            'url' => 'https://idir.test'
        ]);

        $link = Link::makeFactory()->backlink()->create();

        $backlink = DirBacklink::makeFactory()->for($dir)->for($link)->create([
            'url' => 'https://idir.test/page-with-backlink'
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertDatabaseHas('dirs', [
            'title' => $this->setUpDir()['title']
        ]);
    }

    public function testApiDirUpdateFieldsInDatabase()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(
            route('api.dir.update', [$dir->id, $newGroup->id]),
            ($setUpDir = $this->setUpDir()) + $this->setUpFields($newGroup)
        );

        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertStatus(HttpResponse::HTTP_OK);

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

    public function testApiDirUpdateExistFieldsInDatabase()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->create();

        foreach (['input', 'textarea'] as $type) {
            $fields[] = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();
        }

        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)
            ->hasAttached($fields, ['value' => json_encode('Commodo laborum irure mollit laborum occaecat adipisicing dolore.')])
            ->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

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
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirUpdateValidationNoExistPaymentFail()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => rand(1, 1000)
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirUpdateNewGroupPaymentInDatabase()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => $price->id
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::PAYMENT_INACTIVE
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
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'title' => $this->setUpDir()['title'],
            'status' => Status::INACTIVE
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
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->pending()->withCategory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payment_type']);
    }

    public function testApiDirUpdateValidationPaymentAutoCodeSmsPass()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        $newGroup = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->codeSms()->seasonal()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

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

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::CODE_SMS,
            'payment_code_sms' => $price->id,
            'code_sms' => Str::random(6)
        ] + $this->setUpDir());

        $dir->refresh();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass(),
            'order_id' => $price->id,
            'status' => Status::ACTIVE
        ]);

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertTrue($dir->privileged_to !== null);
    }
}
