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

namespace N1ebieski\IDir\Tests\Feature\Api\Dir\Update;

use Tests\TestCase;
use Mockery\MockInterface;
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
use N1ebieski\IDir\ValueObjects\Group\Slug;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\ValueObjects\Field\Type as FieldType;
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
        /** @var Category */
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
        $fields = [];

        foreach (FieldType::getAvailable() as $type) {
            /** @var Field */
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

            $fields['field'][$field->id] = match ($field->type->getValue()) {
                FieldType::INPUT, FieldType::TEXTAREA => 'Cupidatat magna enim officia non sunt esse qui Lorem quis.',

                // @phpstan-ignore-next-line
                FieldType::SELECT => $field->options->options[0],

                // @phpstan-ignore-next-line
                FieldType::MULTISELECT, FieldType::CHECKBOX => array_slice($field->options->options, 0, 2),

                FieldType::IMAGE => UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000),

                default => throw new \InvalidArgumentException("The Type '{$field->type}' not found")
            };
        }

        return $fields;
    }

    public function testApiDirUpdateAsGuest(): void
    {
        /** @var Group */
        // @phpstan-ignore-next-line
        $defaultGroup = Group::make()->makeCache()->rememberBySlug(Slug::default());

        $response = $this->putJson(route('api.dir.update', [
            rand(1, 1000),
            $defaultGroup->id
        ]));

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testApiDirUpdateAsUserWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);

        $response->assertJson(['message' => 'User does not have the right permissions.']);
    }

    public function testApiDirUpdateAsUserWithoutAbility(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);

        $response->assertJson(['message' => 'Invalid ability provided.']);
    }

    public function testApiDirUpdateForeignDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->for($group)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $dir->group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateNoExistDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        // @phpstan-ignore-next-line
        $defaultGroup = Group::make()->makeCache()->rememberBySlug(Slug::default());

        $response = $this->putJson(route('api.dir.update', [
            rand(1, 1000),
            $defaultGroup->id
        ]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirUpdateNoExistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, rand(2, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirUpdatePrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $privateGroup = Group::makeFactory()->private()->create();

        /** @var Group */
        $publicGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($publicGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $privateGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateMaxModelsNewGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->maxModels()->create();

        Dir::makeFactory()->withUser()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dirInOldGroup = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dirInOldGroup->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirUpdateMaxModelsOldGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->maxModels()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_OK);
    }

    public function testApiDirUpdateValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredUrl()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
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

    public function testApiDirUpdateValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredUrl()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['url']);
    }

    public function testApiDirUpdateValidationCategoriesFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        /** @var Collection<Category> */
        $categories = Category::makeFactory()->count(3)->active()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'categories' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['categories']);
    }

    public function testApiDirUpdateValidationFieldsFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

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

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors($fields);
    }

    public function testApiDirUpdateValidationBacklinkFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        Link::makeFactory()->backlink()->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['backlink', 'backlink_url']);
    }

    public function testApiDirUpdateInResource(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $response->assertJsonFragment([
            'title' => $this->setUpDir()['title'],
            'content_html' => "<p>{$this->setUpDir()['content_html']}</p>",
            'url' => $this->setUpDir()['url']
        ]);
    }

    public function testApiDirUpdateUserAsUserInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->withoutUrl()->create();

        /** @var User */
        $newUser = User::makeFactory()->user()->create();

        /** @var Dir */
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

    public function testApiDirUpdateUserAsAdminInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->admin()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->withoutUrl()->create();

        /** @var User */
        $newUser = User::makeFactory()->user()->create();

        /** @var Dir */
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

    public function testApiDirUpdateBacklinkInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create([
            'url' => 'https://idir.test'
        ]);

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        $backlinkUrl = 'https://idir.test/page-with-backlink';

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($link, $backlinkUrl) {
            $mock->shouldReceive('request')->once()->with('GET', $backlinkUrl, ['verify' => false])
                ->andReturn(
                    new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">backlink</a>')
                );
        });

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'backlink' => $link->id,
            'backlink_url' => $backlinkUrl
        ]);

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('dirs_backlinks', [
            'dir_id' => $dir->id,
            'link_id' => $link->id,
            'url' => $backlinkUrl
        ]);
    }

    public function testApiDirUpdateExistBacklinkInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredBacklink()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create([
            'url' => 'https://idir.test'
        ]);

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        DirBacklink::makeFactory()->for($dir)->for($link)->create([
            'url' => 'https://idir.test/page-with-backlink'
        ]);

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertDatabaseHas('dirs', [
            'title' => $this->setUpDir()['title']
        ]);
    }

    public function testApiDirUpdateFieldsInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(
            route('api.dir.update', [$dir->id, $newGroup->id]),
            ($setUpDir = $this->setUpDir()) + $this->setUpFields($newGroup)
        );

        /** @var Dir */
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

    public function testApiDirUpdateExistFieldsInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        foreach ([FieldType::INPUT, FieldType::TEXTAREA] as $type) {
            $fields[] = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();
        }

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)
            ->hasAttached(collect($fields), [
                'value' => json_encode('Commodo laborum irure mollit laborum occaecat adipisicing dolore.')
            ])
            ->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        /** @var Dir */
        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('fields_values', [
            'model_id' => $dir->id,
            'model_type' => $dir->getMorphClass()
        ]);
    }

    public function testApiDirUpdateValidationPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirUpdateValidationNoExistPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->create();

        Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => rand(1, 1000)
        ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['payment_transfer']);
    }

    public function testApiDirUpdateNewGroupPaymentInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => $price->id
        ] + $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

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

        $response->assertJsonFragment(['uuid' => $payment->uuid]);
    }

    public function testApiDirUpdateOldGroupWithoutPaymentInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        /** @var Price */
        $price = Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_OK);

        /** @var Dir */
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
            'status' => PaymentStatus::PENDING
        ]);
    }

    public function testApiDirUpdatePendingValidationPaymentFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $group = Group::makeFactory()->public()->applyInactive()->create();

        Price::makeFactory()->transfer()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->pending()->withCategory()->for($group)->for($user)->create();

        $response = $this->putJson(route('api.dir.update', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['payment_type']);
    }

    public function testApiDirUpdateValidationPaymentAutoCodeSmsPass(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.edit']);

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->applyActive()->create();

        /** @var Price */
        $price = Price::makeFactory()->codeSms()->seasonal()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

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
