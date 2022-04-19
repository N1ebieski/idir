<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Field;

use Tests\TestCase;
use N1ebieski\IDir\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FieldTest extends TestCase
{
    use DatabaseTransactions;

    public function testFieldGusNotFound()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.field.gus'), [
            'type' => 'nip',
            'number' => 111111111
        ]);

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
        $response->assertJsonValidationErrors(['gus']);
    }

    public function testFieldGusNotValid()
    {
        $user = User::makeFactory()->user()->create();

        Auth::login($user);

        $response = $this->post(route('web.field.gus'), [
            'type' => 'nip',
            'number' => 'asjkdasjkdajskdjaskdjskdsd'
        ]);

        $response->assertStatus(HttpResponse::HTTP_FOUND);
        $response->assertSessionHasErrors(['number']);
    }

    public function testFieldGusValid()
    {
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
