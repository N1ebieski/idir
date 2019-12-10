<?php

namespace N1ebieski\IDir\Tests\Feature\Web;

use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Code;
use N1ebieski\IDir\Models\Field\Group\Field;
use Tests\TestCase;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * [DirTest description]
 */
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
    protected function dirSetup() : array
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
    protected function fieldsSetup(Group $group) : array
    {
        foreach (static::FIELD_TYPES as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($group);

            $key = $field->id;
            if (in_array($field->type, ['input', 'textarea'])) {
                $fields['field'][$key] = 'dasdasdadasds23238dfd8fdshjfdshfjsdhfjsdhfsdjf';
            } else if ($field->type === 'select') {
                $fields['field'][$key] = $field->options->options[0];
            } else if (in_array($field->type, ['multiselect', 'checkbox'])) {
                $fields['field'][$key] = array_slice($field->options->options, 0, 2);
            } else if ($field->type === 'image') {
                $fields['field'][$key] = UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000);
                $this->mock(\N1ebieski\IDir\Libs\File::class, function($mock) {
                    $mock->shouldReceive('moveFromTemp')->andReturn(true);
                })->makePartial();
            }
        }

        return $fields;
    }

    public function test_dir_create_group_as_guest()
    {
        $response = $this->get(route('web.dir.create_1'));

        $response->assertRedirect(route('login'));
    }

    public function test_dir_create_group()
    {
        $user = factory(User::class)->states('user')->create();

        $public_groups = factory(Group::class, 3)->states('public')->create();
        $private_group = factory(Group::class)->states('private')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.dir.create_1'));

        $response->assertOk()->assertViewIs('idir::web.dir.create.1');
        $response->assertSee(route('web.dir.create_2', [$public_groups[2]->id]));
        $response->assertSeeText($public_groups[2]->name);
        $response->assertDontSee($private_group->name);
    }

    public function test_dir_create_form_as_guest()
    {
        $response = $this->get(route('web.dir.create_2', [34]));

        $response->assertRedirect(route('login'));
    }

    public function test_dir_create_form_noexist_group()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.dir.create_2', [34]));

        $response->assertStatus(404);
    }

    public function test_dir_create_form_private_group()
    {
        $user = factory(User::class)->states('user')->create();

        $group = factory(Group::class)->states('private')->create();

        Auth::login($user, true);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(403);
    }

    public function test_dir_create_form_max_models_group()
    {
        $user = factory(User::class)->states('user')->create();

        $group = factory(Group::class)->states(['public', 'max_models'])->create();
        $dir = factory(Dir::class)->states(['with_user'])->create(['group_id' => $group->id]);

        Auth::login($user, true);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(403);
    }

    public function test_dir_create_form_max_models_daily_group()
    {
        $user = factory(User::class)->states('user')->create();

        $group = factory(Group::class)->states(['public', 'max_models_daily'])->create();
        $dir = factory(Dir::class)->states(['with_user'])->create(['group_id' => $group->id]);

        Auth::login($user, true);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(403);
    }

    public function test_dir_create_form()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states('public', 'additional options for editing content')->create();

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertOk()->assertViewIs('idir::web.dir.create.2');
        $response->assertSee('trumbowyg');
        $response->assertSee(route('web.dir.store_2', [$group->id]));
    }

    public function test_dir_store_form_as_guest()
    {
        $response = $this->post(route('web.dir.store_2', [34]));

        $response->assertRedirect(route('login'));
    }

    public function test_dir_store_form_noexist_group()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $response = $this->post(route('web.dir.store_2', [34]));

        $response->assertStatus(404);
    }

    public function test_dir_store_form_private_group()
    {
        $user = factory(User::class)->states('user')->create();

        $group = factory(Group::class)->states('private')->create();

        Auth::login($user, true);

        $response = $this->post(route('web.dir.store_2', [$group->id]));

        $response->assertStatus(403);
    }

    public function test_dir_store_form_validation_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]));

        $response->assertSessionHasErrors(['categories', 'title', 'content', 'url']);
    }

    public function test_dir_store_form_validation_url_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function test_dir_store_form()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)
            ->states([
                'public',
                'required_url',
                'additional options for editing content'
            ])->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]), $this->dirSetup());

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
        $response->assertSessionHas('dir.title', 'dasdasdasd');
    }

    public function test_dir_create_summary_as_guest()
    {
        $response = $this->get(route('web.dir.create_3', [34]));

        $response->assertRedirect(route('login'));
    }

    public function test_dir_store_summary_as_guest()
    {
        $response = $this->post(route('web.dir.store_3', [34]));

        $response->assertRedirect(route('login'));
    }

    public function test_dir_store_summary_validation_url_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_url'])->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_categories_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'max_cats'])->create();

        $category = factory(Category::class, 3)->states('active')->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'categories' => $category->pluck('id')->toArray()
        ]);

        $response->assertSessionHasErrors(['categories']);
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_fields_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'max_cats'])->create();

        foreach (static::FIELD_TYPES as $type) {
            $field = factory(Field::class)->states([$type, 'public'])->create();
            $field->morphs()->attach($group);

            $fields[] = "field.{$field->id}";
        }

        $response = $this->post(route('web.dir.store_3', [$group->id]), []);

        $response->assertSessionHasErrors($fields);
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_backlink_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $link = factory(Link::class)->states('backlink')->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]), []);

        $response->assertSessionHasErrors('backlink');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_backlink_pass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'required_backlink'])->create();

        $link = factory(Link::class)->states('backlink')->create();

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl')->andReturn(
                new GuzzleResponse(200, [], '<a href="' . $link->url . '">dadasdasd</a>')
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'backlink' => $link->id,
            'backlink_url' => 'http://dadadad.pl'
        ]);

        $response->assertSessionDoesntHaveErrors('backlink_url');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_fields()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_active', 'required_url'])->create();

        $response = $this->post(route('web.dir.store_3', [$group->id]),
            ($dirSetup = $this->dirSetup()) + $this->fieldsSetup($group));

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

        $response->assertRedirect(route('web.dir.show', [$dir->slug]));
    }

    public function test_dir_store_summary_validation_payment_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'transfer'
        ]);

        $response->assertSessionHasErrors('payment_transfer');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_noexist_payment_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'transfer',
            'payment_transfer' => 23232
        ]);

        $response->assertSessionHasErrors('payment_transfer');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_payment()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();
        $price = factory(Price::class)->states(['transfer'])->make();
        $price->group()->associate($group)->save();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
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
            'price_id' => $price->id,
            'status' => 2
        ]);

        $payment = Payment::orderBy('id', 'desc')->first();

        $response->assertSessionDoesntHaveErrors('payment_transfer');
        $response->assertRedirect(route('web.payment.dir.show', [$payment->id]));
    }

    public function test_dir_store_summary_validation_payment_code_sms_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group)->save();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id
        ] + $this->dirSetup());

        $response->assertSessionHasErrors('code_sms');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_payment_auto_code_sms_pass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group)->save();

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

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => 'dsadasd7a8s'
        ] + $this->dirSetup());

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'price_id' => $price->id,
            'status' => 1
        ]);

        $response->assertSessionDoesntHaveErrors('code_sms');
        $this->assertTrue($dir->privileged_at !== null);
        $response->assertRedirect(route('web.dir.show', [$dir->slug]));
    }

    public function test_dir_store_summary_validation_payment_auto_code_sms_error()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group)->save();

        $this->mock(GuzzleClient::class, function ($mock) use ($price) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(404, [], json_encode([
                    'error' => 'Something wrong'
                ]))
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => 'dsadasd7a8s'
        ] + $this->dirSetup());

        $response->assertSessionHasErrors('code_sms');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_payment_code_transfer_fail()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public'])->create();
        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group)->save();

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id
        ] + $this->dirSetup());

        $response->assertSessionHasErrors('code_transfer');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_payment_auto_code_transfer_pass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group)->save();

        $this->mock(GuzzleClient::class, function ($mock) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(200, [], "OK\n23782738273")
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id,
            'code_transfer' => 'dsadasd7a8s'
        ] + $this->dirSetup());

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'price_id' => $price->id,
            'status' => 0
        ]);

        $response->assertSessionDoesntHaveErrors('code_transfer');
        $this->assertTrue($dir->privileged_at === null && $dir->status === 0);
        $response->assertRedirect(route('web.dir.create_1'));
    }

    public function test_dir_store_summary_validation_payment_auto_code_transfer_error()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group)->save();

        $this->mock(GuzzleClient::class, function ($mock) {
            $mock->shouldReceive('request')->andReturn(
                new GuzzleResponse(200, [], "ERROR")
            );
        });

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id,
            'code_transfer' => 'dsadasd7a8s'
        ] + $this->dirSetup());

        $response->assertSessionHasErrors('code_transfer');
        $response->assertRedirect(route('web.dir.create_3', [$group->id]));
    }

    public function test_dir_store_summary_validation_payment_local_code_transfer_pass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_inactive'])->create();

        $price = factory(Price::class)->states(['code_transfer'])->make();
        $price->group()->associate($group)->save();

        $code = factory(Code::class)->states(['one'])->make();
        $code->price()->associate($price)->save();

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_transfer',
            'payment_code_transfer' => $price->id,
            'code_transfer' => $code->code
        ] + $this->dirSetup());

        $this->assertDatabaseMissing('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'price_id' => $price->id,
            'status' => 0
        ]);

        $response->assertSessionDoesntHaveErrors('code_transfer');
        $this->assertTrue($dir->privileged_at === null && $dir->status === 0);
        $response->assertRedirect(route('web.dir.create_1'));
    }

    public function test_dir_store_summary_validation_payment_local_code_sms_pass()
    {
        $user = factory(User::class)->states('user')->create();

        Auth::login($user, true);

        $group = factory(Group::class)->states(['public', 'apply_active'])->create();

        $price = factory(Price::class)->states(['code_sms'])->make();
        $price->group()->associate($group)->save();

        $code = factory(Code::class)->states(['two'])->make();
        $code->price()->associate($price)->save();

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity
        ]);

        $response = $this->post(route('web.dir.store_3', [$group->id]), [
            'payment_type' => 'code_sms',
            'payment_code_sms' => $price->id,
            'code_sms' => $code->code
        ] + $this->dirSetup());

        $this->assertDatabaseHas('codes', [
            'price_id' => $price->id,
            'code' => $code->code,
            'quantity' => $code->quantity - 1
        ]);

        $dir = Dir::orderBy('id', 'desc')->first();

        $this->assertDatabaseHas('payments', [
            'model_id' => $dir->id,
            'model_type' => 'N1ebieski\\IDir\\Models\\Dir',
            'price_id' => $price->id,
            'status' => 1
        ]);

        $response->assertSessionDoesntHaveErrors('code_sms');
        $this->assertTrue($dir->privileged_at !== null && $dir->status === 1);
        $response->assertRedirect(route('web.dir.show', [$dir->slug]));
    }
}
