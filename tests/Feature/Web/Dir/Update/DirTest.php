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
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Tests\Feature\Traits\HasDir;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Tests\Feature\Traits\HasFields;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\ValueObjects\Field\Type as FieldType;
use N1ebieski\IDir\ValueObjects\Payment\Status as PaymentStatus;

class DirTest extends TestCase
{
    use HasDir;
    use HasFields;
    use DatabaseTransactions;

    public function testDirEdit1AsGuest(): void
    {
        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistDirEdit1(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testForeignDirEdit1(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->withUser()->create();

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit1(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Collection<Group>|array<Group> */
        $publicGroups = Group::makeFactory()->count(3)->public()->create();

        /** @var Group */
        $privateGroup = Group::makeFactory()->private()->create([
            'name' => 'Private Group'
        ]);

        /** @var Dir */
        $dir = Dir::makeFactory()->for($user)->for($publicGroups[1])->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response->assertOk()
            ->assertViewIs('idir::web.dir.edit.1')
            ->assertSee(route('web.dir.edit_2', [$dir->id, $publicGroups[1]->id]))
            ->assertSee($publicGroups[1]->name)
            ->assertDontSee($privateGroup->name);
    }

    public function testDirEdit2AsGuest(): void
    {
        $response = $this->get(route('web.dir.edit_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirEdit2NoexistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit2NoexistDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->get(route('web.dir.edit_2', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit2Foreign(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit2PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $privateGroup = Group::makeFactory()->private()->create();

        /** @var Group */
        $publicGroup = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($publicGroup)->for($user)->create();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $privateGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit2MaxModelsNewGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $newGroup = Group::makeFactory()->public()->maxModels()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->for($newGroup)->create();

        /** @var Group */
        $oldGroup = Group::makeFactory()->public()->create();

        Dir::makeFactory()->for($oldGroup)->for($user)->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit2MaxModelsOldGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->maxModels()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        Auth::login($user);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.2')
            ->assertSee($dir->title);
    }

    public function testDirEdit2(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->additionalOptionsForEditingContent()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.2')
            ->assertSee('trumbowyg')
            ->assertSee(route('web.dir.update_2', [$dir->id, $group->id]));
    }

    public function testDirUpdate2AsGuest(): void
    {
        $response = $this->put(route('web.dir.update_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirUpdate2NoexistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate2NoexistDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->put(route('web.dir.update_2', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate2Foreign(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirUpdate2PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->private()->create();

        Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirUpdate2ValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $groupWithUrl = Group::makeFactory()->public()->requiredUrl()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withoutUrl()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->put(route('web.dir.update_2', [$dir->id, $groupWithUrl->id]));

        $response2->assertSessionHasErrors(['url', 'categories']);
    }

    public function testDirUpdate2ValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $groupWithUrl = Group::makeFactory()->public()->requiredUrl()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withoutUrl()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $groupWithUrl->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testDirUpdate2(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $groupWithUrl = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withoutUrl()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $groupWithUrl->id]), $this->setUpDir());

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $groupWithUrl->id]));

        $response->assertSessionHas("dirId.{$dir->id}.title", $this->setUpDir()['title']);
    }

    public function testDirEdit3AsGuest(): void
    {
        $response = $this->get(route('web.dir.edit_3', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirEdit3NoexistGroup(): void
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

    public function testDirEdit3NoexistDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->get(route('web.dir.edit_3', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit3Foreign(): void
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

    public function testDirEdit3OldPaidGroup(): void
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

    public function testDirEdit3NewPaidGroup(): void
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

    public function testDirUpdate3AsGuest(): void
    {
        $response = $this->put(route('web.dir.update_3', [34, 43]));

        $response->assertRedirect(route('login'));
    }

    public function testDirUpdate3NoexistGroup(): void
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

    public function testDirUpdate3NoexistDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->put(route('web.dir.update_3', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate3Foreign(): void
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

    public function testDirUpdate3PrivateGroup(): void
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

    public function testDirUpdate3ValidationUrlFail(): void
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

    public function testDirStoreSummaryValidationCategoriesFail(): void
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

    public function testDirStoreSummaryValidationFieldsFail(): void
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

    public function testDirStoreSummaryValidationBacklinkFail(): void
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

    public function testDirStoreSummaryValidationBacklinkPass(): void
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

    public function testDirUpdate3Fields(): void
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

    public function testDirUpdate3ValidationPaymentFail(): void
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

    public function testDirUpdate3ValidationNoexistPaymentFail(): void
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

    public function testDirUpdate3NewGroupPayment(): void
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

    public function testDirUpdate3OldGroupWithoutPayment(): void
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

    public function testPendingDirUpdate3OldGroupPaymentFail(): void
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

    public function testDirUpdate3ValidationPaymentAutoCodeSmsPass(): void
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
