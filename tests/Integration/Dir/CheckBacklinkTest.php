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

namespace N1ebieski\IDir\Tests\Integration\Dir;

use Tests\TestCase;
use Mockery\MockInterface;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\Crons\Dir\BacklinkCron;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Http\Response as HttpResponse;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Mail\DirBacklink\BacklinkNotFoundMail;

class CheckBacklinkTest extends TestCase
{
    use DatabaseTransactions;

    public function testDeactivateQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredBacklink()->create();

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create();

        /** @var DirBacklink */
        $dirBacklink = DirBacklink::makeFactory()->for($link)->for($dir)->create(['url' => 'http://dadadad.pl']);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id,
        ]);

        Config::set('idir.dir.backlink.max_attempts', 1);

        $this->mock(GuzzleClient::class, function (MockInterface $mock) {
            $mock->shouldReceive('request')->once()
                ->with('GET', 'http://dadadad.pl', ['verify' => false])
                ->andReturn(
                    new GuzzleResponse(HttpResponse::HTTP_OK, [], 'dadasdasd sdsajdhjashdj')
                );
        });

        Mail::fake();

        $schedule = app()->make(BacklinkCron::class);
        $schedule();

        Mail::assertSent(BacklinkNotFoundMail::class, function (BacklinkNotFoundMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::BACKLINK_INACTIVE,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseHas('dirs_backlinks', [
            'id' => $dirBacklink->id,
            'attempts' => 1
        ]);
    }

    public function testActivateQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredBacklink()->create();

        /** @var Link */
        $link = Link::makeFactory()->backlink()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->backlinkInactive()->for($group)->create();

        /** @var DirBacklink */
        $dirBacklink = DirBacklink::makeFactory()->for($link)->for($dir)->create([
            'url' => 'http://dadadad.pl',
            'attempts' => 5
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::BACKLINK_INACTIVE,
            'group_id' => $group->id,
        ]);

        Config::set('idir.dir.backlink.max_attempts', 1);

        $this->mock(GuzzleClient::class, function (MockInterface $mock) use ($link) {
            $mock->shouldReceive('request')->once()
                ->with('GET', 'http://dadadad.pl', ['verify' => false])
                ->andReturn(
                    new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">dadasdasd</a>')
                );
        });

        Mail::fake();

        $schedule = app()->make(BacklinkCron::class);
        $schedule();

        Mail::assertNotSent(BacklinkNotFoundMail::class, function (BacklinkNotFoundMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseHas('dirs_backlinks', [
            'id' => $dirBacklink->id,
            'attempts' => 0
        ]);
    }
}
