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
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\IDir\Testing\Traits\Dir\HasDir;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Update2DirTest extends TestCase
{
    use HasDir;
    use DatabaseTransactions;

    public function testEdit2AsGuest(): void
    {
        $response = $this->get(route('web.dir.edit_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testEdit2NoExistGroup(): void
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

    public function testEdit2NoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->get(route('web.dir.edit_2', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit2Foreign(): void
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

    public function testEdit2PrivateGroup(): void
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

    public function testEdit2MaxModelsNewGroup(): void
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

    public function testEdit2MaxModelsOldGroup(): void
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

    public function testEdit2(): void
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

    public function testUpdate2AsGuest(): void
    {
        $response = $this->put(route('web.dir.update_2', [34, 23]));

        $response->assertRedirect(route('login'));
    }

    public function testUpdate2NoExistGroup(): void
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

    public function testUpdate2NoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        $response = $this->put(route('web.dir.update_2', [34, $group->id]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testUpdate2Foreign(): void
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

    public function testUpdate2PrivateGroup(): void
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

    public function testUpdate2ValidationFail(): void
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

    public function testUpdate2ValidationUrlFail(): void
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

    public function testUpdate2(): void
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
}
