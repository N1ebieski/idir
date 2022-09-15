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

    public function testApiDirDestroyAsGuest(): void
    {
        $response = $this->deleteJson(route('api.dir.destroy', [rand(1, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function testApiDirDestroyAsUserWithoutPermission(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);

        $response->assertJson(['message' => 'User does not have the right permissions.']);
    }

    public function testApiDirDestroyAsUserWithoutAbility(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->for($group)->for($user)->create();

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);

        $response->assertJson(['message' => 'Invalid ability provided.']);
    }

    public function testApiDirDestroyForeignDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.delete']);

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->for($group)->create();

        $response = $this->deleteJson(route('api.dir.destroy', [$dir->id]));

        $response->assertStatus(HttpResponse::HTTP_FORBIDDEN);
    }

    public function testApiDirDestroyNoExistDir(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.delete']);

        $response = $this->deleteJson(route('api.dir.destroy', [rand(1, 1000)]));

        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
    }

    public function testApiDirDestroyAsUserInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        Sanctum::actingAs($user, ['api.dirs.delete']);

        Mail::fake();

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
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

        Mail::assertSent(DeletedMail::class, function (DeletedMail $mail) use ($user, $reason) {
            $mail->build();

            $this->assertFalse($mail->reason === $reason);

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseMissing('dirs', [
            'id' => $dir->id,
            'user_id' => $user->id
        ]);
    }

    public function testApiDirDestroyAsAdminInDatabase(): void
    {
        /** @var User */
        $user = User::makeFactory()->user()->api()->create();

        /** @var User */
        $admin = User::makeFactory()->user()->api()->admin()->create();

        Sanctum::actingAs($admin, ['api.dirs.delete']);

        Mail::fake();

        /** @var Group */
        $group = Group::makeFactory()->public()->create();

        /** @var Dir */
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

        Mail::assertSent(DeletedMail::class, function (DeletedMail $mail) use ($user, $reason) {
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
