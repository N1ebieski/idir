<?php

namespace N1ebieski\IDir\Tests\Feature\Api\Dir\Delete;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mail\Dir\DeletedMail;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirTest extends TestCase
{
    use DatabaseTransactions;

    public function testApiDirDestroyAsGuest()
    {
        $response = $this->deleteJson(route('api.dir.destroy', [rand(1, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testApiDirDestroyAsUserWithoutPermission()
    {
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertJson(['message' => 'User does not have the right permissions.']);
    }

    public function testApiDirDestroyAsUserWithoutAbility()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
        $response->assertJson(['message' => 'Invalid ability provided.']);
    }

    public function testApiDirDestroyForeignDir()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.delete']);

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withUser()->for($group)->create();

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirDestroyNoExistDir()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.delete']);

        $response = $this->deleteJson(route('api.dir.destroy', [rand(1, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirDestroyAsUserInDatabase()
    {
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.delete']);

        Mail::fake();

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)->create();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'user_id' => $user->id
        ]);

        $reason = 'Eiusmod dolore irure adipisicing adipisicing';

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]), [
            'reason' => $reason
        ]);

        $response->assertStatus(HttpResponse::HTTP_NO_CONTENT);

        Mail::assertSent(DeletedMail::class, function ($mail) use ($user, $reason) {
            $mail->build();

            $this->assertFalse($mail->reason === $reason);

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseMissing('dirs', [
            'id' => $dir->id,
            'user_id' => $user->id
        ]);
    }

    public function testApiDirDestroyAsAdminInDatabase()
    {
        $user = User::makeFactory()->user()->api()->create();

        $admin = User::makeFactory()->user()->api()->admin()->create();

        Sanctum::actingAs($admin, ['api.dirs.delete']);

        Mail::fake();

        $group = Group::makeFactory()->public()->create();

        $dir = Dir::makeFactory()->withCategory()->for($group)->for($user)->create();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'user_id' => $user->id
        ]);

        $reason = 'Eiusmod dolore irure adipisicing adipisicing';

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]), [
            'reason' => $reason
        ]);

        $response->assertStatus(HttpResponse::HTTP_NO_CONTENT);

        Mail::assertSent(DeletedMail::class, function ($mail) use ($user, $reason) {
            $mail->build();

            $this->assertTrue($mail->reason === $reason);

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseMissing('dirs', [
            'id' => $dir->id,
            'user_id' => $user->id
        ]);
    }
}