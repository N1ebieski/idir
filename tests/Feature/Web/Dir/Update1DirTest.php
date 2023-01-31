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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Update1DirTest extends TestCase
{
    use DatabaseTransactions;

    public function testEdit1AsGuest(): void
    {
        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertRedirect(route('login'));
    }

    public function testEdit1NoExist(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->get(route('web.dir.edit_1', [232]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testEdit1Foreign(): void
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

    public function testEdit1(): void
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
}
