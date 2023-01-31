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

namespace N1ebieski\IDir\Tests\Feature\Web\Dir;

use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Testing\Traits\Dir\HasDir;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Create2DirTest extends TestCase
{
    use HasDir;
    use DatabaseTransactions;

    public function testCreate2NoExistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [34]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testCreate2PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->private()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate2MaxModelsGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->maxModels()->create();

        Dir::makeFactory()->withUser()->for($group)->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate2MaxModelsDailyGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->public()->maxModelsDaily()->create();

        Dir::makeFactory()->withUser()->for($group)->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testCreate2(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->additionalOptionsForEditingContent()->create();

        $response = $this->get(route('web.dir.create_2', [$group->id]));

        $response->assertOk()
            ->assertViewIs('idir::web.dir.create.2')
            ->assertSee('trumbowyg')
            ->assertSee(route('web.dir.store_2', [$group->id]));
    }

    public function testStore2NoExistGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.dir.store_2', [34]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testStore2PrivateGroup(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Group */
        $group = Group::makeFactory()->private()->create();

        Auth::login($user);

        $response = $this->post(route('web.dir.store_2', [$group->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testStore2ValidationFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]));

        $response->assertSessionHasErrors(['categories', 'title', 'content', 'url']);
    }

    public function testStore2ValidationUrlFail(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]), [
            'url' => 'dadasdasdasdasdsa23232'
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function testStore2(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->requiredUrl()->additionalOptionsForEditingContent()->create();

        $response = $this->post(route('web.dir.store_2', [$group->id]), $this->setUpDir());

        $response->assertRedirect(route('web.dir.create_3', [$group->id]));

        $response->assertSessionHas('dir.title', $this->setUpDir()['title']);
    }
}
