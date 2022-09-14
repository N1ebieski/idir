<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Dir\Update;

use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;
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
        $category = Category::makeFactory()->active()->create();

        return [
            'title' => 'dasdasdasd',
            'tags' => ['dasdasd', 'dasdasdas', 'dasdasdas'],
            'content_html' => 'dasdasdhasj hsjd <b>sdasdasd</b> asdasdasd
            dshajdashjd hasjdasjdhasjdhja dhsajdhasjdhasjdhjasdhajsd asdasdjsakdjsak
            dsadjaksdjaskdjaskd sdasasd asdasdasdasdd sadasjdjashd jashdjasdhjas hjsa
            djashdjashdjashdjashd jashdjashdjsa dfdsfsdfsdfsfsdf sad87238728372837827',
            'categories' => [$category->id],
            'url' => 'http://dasdasdasdas.pl'
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

            $fields['field'][$field->id] = match ($field->type) {
                FieldType::INPUT, FieldType::TEXTAREA => 'Cupidatat magna enim officia non sunt esse qui Lorem quis.',

                FieldType::SELECT => $field->options->options[0],

                FieldType::MULTISELECT, FieldType::CHECKBOX => array_slice($field->options->options, 0, 2),

                FieldType::IMAGE => UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000)
            };
        }

        return $fields;
    }

    public function testDirEdit1AsGuest()
    {
        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertRedirect(route('login'));
    }

    public function testNoexistDirEdit1()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testForeignDirEdit1()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->withUser()->create();

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit1()
    {
        $user = User::makeFactory()->user()->create();

        $publicGroups = Group::makeFactory()->count(3)->public()->create();

        $privateGroup = Group::makeFactory()->private()->create([
            'name' => 'Private Group'
        ]);

        $dir = Dir::makeFactory()->for($user)->for($publicGroups[1])->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response->assertOk()->assertViewIs('idir::web.dir.edit.1');
        $response->assertSee(route('web.dir.edit_2', [$dir->id, $publicGroups[1]->id]));
        $response->assertSee($publicGroups[1]->name);
        $response->assertDontSee($privateGroup->name);
    }

    public function testDirEdit2AsGuest()
    {
        $response = $this->get(route('web.dir.edit_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirEdit2NoexistGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit2NoexistDir()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $response = $this->get(route('web.dir.edit_2', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit2Foreign()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit2PrivateGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $privateGroup = Group::makeFactory()->private()->create();

        $publicGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($publicGroup)->for($user)->create();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $privateGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit2MaxModelsNewGroup()
    {
        $user = User::makeFactory()->user()->create();

        $newGroup = Group::makeFactory()->public()->maxModels()->create();

        $dir = Dir::makeFactory()->withUser()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir2 = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit2MaxModelsOldGroup()
    {
        $user = User::makeFactory()->user()->create();

        $group = Group::makeFactory()->public()->maxModels()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        Auth::login($user);

        $response1 = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.2')
            ->assertSee($dir->title);
    }

    public function testDirEdit2()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->additionalOptionsForEditingContent()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response1 = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response2->assertOk()->assertViewIs('idir::web.dir.edit.2');
        $response2->assertSee('trumbowyg');
        $response2->assertSee(route('web.dir.update_2', [$dir->id, $group->id]));
    }

    public function testDirUpdate2AsGuest()
    {
        $response = $this->put(route('web.dir.update_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirUpdate2NoexistGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate2NoexistDir()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $response = $this->put(route('web.dir.update_2', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate2Foreign()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirUpdate2PrivateGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->private()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirUpdate2ValidationFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $groupWithUrl = Group::makeFactory()->public()->requiredUrl()->create();

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($group)->for($user)->create();

        $response1 = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->put(route('web.dir.update_2', [$dir->id, $groupWithUrl->id]));

        $response2->assertSessionHasErrors(['url', 'categories']);
    }

    public function testDirUpdate2ValidationUrlFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $groupWithUrl = Group::makeFactory()->public()->requiredUrl()->create();

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $groupWithUrl->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testDirUpdate2()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $groupWithUrl = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $groupWithUrl->id]), $this->setUpDir());

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $groupWithUrl->id]));
        $response->assertSessionHas("dirId.{$dir->id}.title", 'dasdasdasd');
    }

    public function testDirEdit3AsGuest()
    {
        $response = $this->get(route('web.dir.edit_3', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirEdit3NoexistGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->get(route('web.dir.edit_3', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit3NoexistDir()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $response = $this->get(route('web.dir.edit_3', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirEdit3Foreign()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirEdit3OldPaidGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->paidSeasonal()->withCategory()->for($group)->for($user)->create();

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.3')
            ->assertDontSee(Type::TRANSFER);
    }

    public function testDirEdit3NewPaidGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->paidSeasonal()->withCategory()->for($oldGroup)->for($user)->create();

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_3', [$dir->id, $newGroup->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.3')
            ->assertSee(Type::TRANSFER);
    }

    public function testDirUpdate3AsGuest()
    {
        $response = $this->put(route('web.dir.update_3', [34, 43]));

        $response->assertRedirect(route('login'));
    }

    public function testDirUpdate3NoexistGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->put(route('web.dir.update_3', [$dir->id, 23]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate3NoexistDir()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $response = $this->put(route('web.dir.update_3', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testDirUpdate3Foreign()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withUser()->create();

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirUpdate3PrivateGroup()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->private()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testDirUpdate3ValidationUrlFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->requiredUrl()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withoutUrl()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testDirStoreSummaryValidationCategoriesFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        $category = Category::makeFactory()->count(3)->active()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'categories' => $category->pluck('id')->toArray()
        ]);

        $response->assertSessionHasErrors(['categories']);
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testDirStoreSummaryValidationFieldsFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->maxCats()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        foreach (FieldType::getAvailable() as $type) {
            $field = Field::makeFactory()->public()->hasAttached($newGroup, [], 'morphs')->{$type}()->create();

            $fields[] = "field.{$field->id}";
        }

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), []);

        $response->assertSessionHasErrors($fields);
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testDirStoreSummaryValidationBacklinkFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create();

        $link = Link::makeFactory()->backlink()->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), []);

        $response->assertSessionHasErrors('backlink');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testDirStoreSummaryValidationBacklinkPass()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->requiredBacklink()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($oldGroup)->for($user)->create([
            'url' => 'http://dadadad.pl'
        ]);

        $link = Link::makeFactory()->backlink()->create();

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl/dasdas', ['verify' => false])->andReturn(
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

    public function testDirUpdate3Fields()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->applyActive()->requiredUrl()->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(
            route('web.dir.update_3', [$dir->id, $newGroup->id]),
            ($setUpDir = $this->setUpDir()) + $this->setUpFields($newGroup)
        );

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

        $response->assertRedirect(route('web.profile.dirs'));
    }

    public function testDirUpdate3ValidationPaymentFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER
        ]);

        $response->assertSessionHasErrors('payment_transfer');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testDirUpdate3ValidationNoexistPaymentFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => 23232
        ]);

        $response->assertSessionHasErrors('payment_transfer');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $newGroup->id]));
    }

    public function testDirUpdate3NewGroupPayment()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $newGroup->id]), [
            'payment_type' => Type::TRANSFER,
            'payment_transfer' => $price->id
        ] + $this->setUpDir());

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

        $payment = Payment::orderBy('created_at', 'desc')->first();

        $response->assertSessionDoesntHaveErrors('payment_transfer');
        $response->assertRedirect(route('web.payment.dir.show', [
            $payment->uuid,
            Config::get('idir.payment.transfer.driver')
        ]));
    }

    public function testDirUpdate3OldGroupWithoutPayment()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), $this->setUpDir());

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

        $payment = Payment::orderBy('created_at', 'desc')->first();

        $response->assertSessionDoesntHaveErrors('payment_transfer');
        $response->assertRedirect(route('web.profile.dirs'));
    }

    public function testPendingDirUpdate3OldGroupPaymentFail()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $group = Group::makeFactory()->public()->applyInactive()->create();

        $price = Price::makeFactory()->transfer()->for($group)->create();

        $dir = Dir::makeFactory()->pending()->withCategory()->for($group)->for($user)->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), $this->setUpDir());

        $response->assertSessionHasErrors('payment_type');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirUpdate3ValidationPaymentAutoCodeSmsPass()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $newGroup = Group::makeFactory()->public()->applyActive()->create();

        $price = Price::makeFactory()->codeSms()->seasonal()->for($newGroup)->create();

        $oldGroup = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($oldGroup)->for($user)->create();

        $this->mock(GuzzleClient::class, function ($mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
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
        $response->assertRedirect(route('web.profile.dirs'));
    }
}
