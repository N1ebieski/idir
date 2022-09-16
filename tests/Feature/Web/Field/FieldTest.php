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

namespace N1ebieski\IDir\Tests\Feature\Web\Field;

use Tests\TestCase;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldTest extends TestCase
{
    use DatabaseTransactions;

    public function testFieldGusNotFound(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.field.gus'), [
            'type' => 'nip',
            'number' => 111111111
        ]);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);

        $response->assertJsonValidationErrors(['gus']);
    }

    public function testFieldGusNotValid(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.field.gus'), [
            'type' => 'nip',
            'number' => 'asjkdasjkdajskdjaskdjskdsd'
        ]);

        $response->assertStatus(HttpResponse::HTTP_FOUND);

        $response->assertSessionHasErrors(['number']);
    }

    public function testFieldGusValid(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.field.gus'), [
            'type' => 'nip',
            'number' => 5832908528
        ]);

        $response->assertOk();

        $response->assertJsonFragment([
            'field.2' => 'ul. Platynowa 15/22, 80-041 Gdańsk'
        ]);
    }
}
