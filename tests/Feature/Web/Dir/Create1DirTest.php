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
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Create1DirTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreate1(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        /** @var Collection<Group>|array<Group> */
        $publicGroups = Group::makeFactory()->count(3)->public()->create();

        /** @var Group */
        $privateGroup = Group::makeFactory()->private()->create([
            'name' => 'Private Group'
        ]);

        Auth::login($user);

        $response = $this->get(route('web.dir.create_1'));

        $response->assertOk()
            ->assertViewIs('idir::web.dir.create.1')
            ->assertSee(route('web.dir.create_2', [$publicGroups[2]->id]))
            ->assertSee($publicGroups[2]->name)
            ->assertDontSee($privateGroup->name);
    }
}
