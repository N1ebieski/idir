<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Dir\Update;

use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\Field\Group\Field;
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
    const FIELD_TYPES = ['input', 'textarea', 'select', 'multiselect', 'checkbox', 'image'];

    /**
     * [dirSetup description]
     * @return array [description]
     */
    protected function dirSetup(): array
    {
        $category = factory(Category::class)->states('active')->create();

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
                $fields['field'][$key] = 'dasdasdadasds23238dfd8fdshjfdshfjsdhfjsdhfsdjf';
            } elseif ($field->type === 'select') {
                $fields['field'][$key] = $field->options->options[0];
            } elseif (in_array($field->type, ['multiselect', 'checkbox'])) {
                $fields['field'][$key] = array_slice($field->options->options, 0, 2);
            } elseif ($field->type === 'image') {
                $fields['field'][$key] = UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000);

                // $this->mock(\N1ebieski\IDir\Utils\File::class, function ($mock) {
                //     $mock->shouldReceive('prepare')->once()->andReturn('vendor/idir/temp/df8s8sd78sd78sdf.jpg');
                //     $mock->shouldReceive('moveFromTemp')->andReturn(true);
                // })->makePartial();
            }
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
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertStatus(404);

        $this->assertTrue(Auth::check());
    }

    public function testForeignDirEdit1()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->states('with_user')->make();
        $dir->group()->associate($group)->save();

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testDirEdit1()
    {
        $user = factory(User::class)->states('user')->create();

        $public_groups = factory(Group::class, 3)->states('public')->create();
        $private_group = factory(Group::class)->states('private')->create();

        $dir = factory(Dir::class)->make();
        $dir->user()->associate($user);
        $dir->group()->associate($public_groups[1])->save();

        Auth::login($user, true);

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response->assertOk()->assertViewIs('idir::web.dir.edit.1');
        $response->assertSee(route('web.dir.edit_2', [$dir->id, $public_groups[1]->id]));
        $response->assertSee($public_groups[1]->name);
        $response->assertDontSee($private_group->name);
    }

    public function testDirEdit2AsGuest()
    {
        $response = $this->get(route('web.dir.edit_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirEdit2NoexistGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, 23]));

        $response->assertStatus(404);
    }

    public function testDirEdit2NoexistDir()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $response = $this->get(route('web.dir.edit_2', [34, $group->id]));

        $response->assertStatus(404);
    }

    public function testDirEdit2Foreign()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->states('with_user')->make();
        $dir->group()->associate($group)->save();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testDirEdit2PrivateGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('private')->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response->assertStatus(403);
    }

    public function testDirEdit2MaxModelsNewGroup()
    {
        $user = factory(User::class)->states('user')->create();

        $new_group = factory(Group::class)->states(['public', 'max_models'])->create();
        $dir = factory(Dir::class)->states(['with_user'])->create(['group_id' => $new_group->id]);

        $old_group = factory(Group::class)->states('public')->create();
        $dir2 = factory(Dir::class)->make();
        $dir2->group()->associate($old_group->id);
        $dir2->user()->associate($user->id)->save();

        Auth::login($user, true);

        $response = $this->get(route('web.dir.edit_2', [$dir->id, $new_group->id]));

        $response->assertStatus(403);
    }

    public function testDirEdit2MaxModelsOldGroup()
    {
        $user = factory(User::class)->states('user')->create();

        $group = factory(Group::class)->states(['public', 'max_models'])->create();
        $dir = factory(Dir::class)->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        Auth::login($user, true);

        $response1 = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_2', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.2')
            ->assertSee($dir->title);
    }

    public function testDirEdit2()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('public', 'additional options for editing content')->create();
        $dir = factory(Dir::class)->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

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
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->put(route('web.dir.update_2', [$dir->id, 23]));

        $response->assertStatus(404);
    }

    public function testDirUpdate2NoexistDir()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $response = $this->put(route('web.dir.update_2', [34, $group->id]));

        $response->assertStatus(404);
    }

    public function testDirUpdate2Foreign()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->states('with_user')->make();
        $dir->group()->associate($group)->save();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testDirUpdate2PrivateGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('private')->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response->assertStatus(403);
    }

    public function testDirUpdate2ValidationFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $response1 = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->put(route('web.dir.update_2', [$dir->id, $group->id]));

        $response2->assertSessionHasErrors(['url', 'categories']);
    }

    public function testDirUpdate2ValidationUrlFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testDirUpdate2()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)
            ->states([
                'public',
                'required_url',
                'additional options for editing content'
            ])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->put(route('web.dir.update_2', [$dir->id, $group->id]), $this->dirSetup());

        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
        $response->assertSessionHas("dirId.{$dir->id}.title", 'dasdasdasd');
    }

    public function testDirEdit3AsGuest()
    {
        $response = $this->get(route('web.dir.edit_3', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testDirEdit3NoexistGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->get(route('web.dir.edit_3', [$dir->id, 23]));

        $response->assertStatus(404);
    }

    public function testDirEdit3NoexistDir()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $response = $this->get(route('web.dir.edit_3', [34, $group->id]));

        $response->assertStatus(404);
    }

    public function testDirEdit3Foreign()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->states('with_user')->make();
        $dir->group()->associate($group)->save();

        $response = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testDirEdit3OldPaidGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['paid_seasonal', 'with_category'])->create([
            'user_id' => $user->id,
            'group_id' => $group->id
        ]);

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.3')
            ->assertDontSee('transfer');
    }

    public function testDirEdit3NewPaidGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $group2 = factory(Group::class)->states(['public'])->create();
        $dir = factory(Dir::class)->states(['paid_seasonal', 'with_category'])->create([
            'user_id' => $user->id,
            'group_id' => $group2->id
        ]);

        $response = $this->get(route('web.dir.edit_1', [$dir->id]));

        $response2 = $this->get(route('web.dir.edit_3', [$dir->id, $group->id]));

        $response2->assertOk()
            ->assertViewIs('idir::web.dir.edit.3')
            ->assertSee('transfer');
    }

    public function testDirUpdate3AsGuest()
    {
        $response = $this->put(route('web.dir.update_3', [34, 43]));

        $response->assertRedirect(route('login'));
    }

    public function testDirUpdate3NoexistGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->put(route('web.dir.update_3', [$dir->id, 23]));

        $response->assertStatus(404);
    }

    public function testDirUpdate3NoexistDir()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $response = $this->put(route('web.dir.update_3', [34, $group->id]));

        $response->assertStatus(404);
    }

    public function testDirUpdate3Foreign()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();

        $dir = factory(Dir::class)->states('with_user')->make();
        $dir->group()->associate($group)->save();

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]));

        $response->assertStatus(403);

        $this->assertTrue(Auth::check());
    }

    public function testDirUpdate3PrivateGroup()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('private')->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]));

        $response->assertStatus(403);
    }

    public function testDirUpdate3ValidationUrlFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('without_url')->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testDirStoreSummaryValidationCategoriesFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'max_cats'])->create();

        $category = factory(Category::class, 3)->states('active')->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'categories' => $category->pluck('id')->toArray()
        ]);

        $response->assertSessionHasErrors(['categories']);
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirStoreSummaryValidationFieldsFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'max_cats'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        foreach (static::FIELD_TYPES as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($group);

            $fields[] = "field.{$field->id}";
        }

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), []);

        $response->assertSessionHasErrors($fields);
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirStoreSummaryValidationBacklinkFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make();
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $link = factory(Link::class)->states('backlink')->create();

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), []);

        $response->assertSessionHasErrors('backlink');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirStoreSummaryValidationBacklinkPass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->make(['url' => 'http://dadadad.pl']);
        $dir->group()->associate($group2->id);
        $dir->user()->associate($user->id)->save();

        $link = factory(Link::class)->states('backlink')->create();

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl/dasdas', ['verify' => false])->andReturn(
                new GuzzleResponse(200, [], '<a href="' . $link->url . '">dadasdasd</a>')
            );
        });

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'backlink' => $link->id,
            'backlink_url' => 'http://dadadad.pl/dasdas'
        ]);

        $response->assertSessionDoesntHaveErrors('backlink_url');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirUpdate3Fields()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_active', 'required_url'])->create();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group2->id,
            'user_id' => $user->id
        ]);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(
            route('web.dir.update_3', [$dir->id, $group->id]),
            ($dirSetup = $this->dirSetup()) + $this->fieldsSetup($group)
        );

        $dir = Dir::orderBy('id', 'desc')->first();

        $response->assertSessionHas('success');
        $this->assertTrue($dir->exists());

        $this->assertDatabaseHas('categories_models', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'category_id' => $dirSetup['categories'][0]
        ]);

        $this->assertDatabaseHas('tags_models', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
        ]);

        $this->assertDatabaseHas('fields_values', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir'
        ]);

        $response->assertRedirect(route('web.profile.edit_dir'));
    }

    public function testDirUpdate3ValidationPaymentFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group2->id,
            'user_id' => $user->id
        ]);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'payment_type' => 'transfer'
        ]);

        $response->assertSessionHasErrors('payment_transfer');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirUpdate3ValidationNoexistPaymentFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group2->id,
            'user_id' => $user->id
        ]);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => 23232
        ]);

        $response->assertSessionHasErrors('payment_transfer');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirUpdate3NewGroupPayment()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group2->id,
            'user_id' => $user->id
        ]);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => $price->id
        ] + $this->dirSetup());

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 2
        ]);

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'order_id' => $price->id,
            'status' => 2
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
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states('with_category')->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), $this->dirSetup());

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 0
        ]);

        $this->assertDatabaseMissing('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'order_id' => $price->id,
            'status' => 2
        ]);

        $payment = Payment::orderBy('created_at', 'desc')->first();

        $response->assertSessionDoesntHaveErrors('payment_transfer');
        $response->assertRedirect(route('web.profile.edit_dir'))->assertSessionHas('success');
    }

    public function testPendingDirUpdate3OldGroupPaymentFail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['pending', 'with_category'])->create([
            'group_id' => $group->id,
            'user_id' => $user->id
        ]);

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), $this->dirSetup());

        $response->assertSessionHasErrors('payment_type');
        $response->assertRedirect(route('web.dir.edit_3', [$dir->id, $group->id]));
    }

    public function testDirUpdate3ValidationPaymentAutoCodeSmsPass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms', 'seasonal'])->make();
        $price->group()->associate($group)->save();

        $group2 = factory(Group::class)->states('public')->create();
        $dir = factory(Dir::class)->states(['with_category'])->create([
            'group_id' => $group2->id,
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

        $this->get(route('web.dir.edit_1', [$dir->id]));

        $response = $this->put(route('web.dir.update_3', [$dir->id, $group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => 'dsadasd7a8s'
        ] + $this->dirSetup());

        $dir->refresh();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'order_id' => $price->id,
            'status' => 1
        ]);

        $response->assertSessionDoesntHaveErrors('code_sms');
        $this->assertTrue($dir->privileged_to !== null);
        $response->assertRedirect(route('web.profile.edit_dir'));
    }
}
