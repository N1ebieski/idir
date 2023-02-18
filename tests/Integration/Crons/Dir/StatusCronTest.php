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

namespace N1ebieski\IDir\Tests\Integration\Crons\Dir;

use Tests\TestCase;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use GuzzleHttp\Handler\MockHandler;
use N1ebieski\IDir\Models\DirStatus;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Crons\Dir\StatusCron;
use GuzzleHttp\Exception\RequestException;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Http\Response as HttpResponse;
use GuzzleHttp\Middleware as GuzzleMiddleware;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StatusCronTest extends TestCase
{
    use DatabaseTransactions;

    public function testStatusKnownParkedDomainQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredUrl()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://parked-domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        DirStatus::makeFactory()->for($dir)->create();

        Config::set('idir.dir.status.max_attempts', 1);
        Config::set('idir.dir.status.parked_domains', [
            'aftermarket.pl',
            'blablabla.pl'
        ]);

        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_FOUND, [
                'Location' => 'https://gzermplatz.aftermarket.pl/redir.php?panel=Market_Auction&params=id%3D2493603&type=auction&id=2493603&medium=direct:direct'
            ]),
            new GuzzleResponse(HttpResponse::HTTP_FOUND, [
                'Location' => 'https://www.aftermarket.pl/aukcja/2493603/?_track=504ea78ba428635f7787e4f49c326f88',
            ]),
            new GuzzleResponse(HttpResponse::HTTP_OK)
        ]);

        $stack = new HandlerStack($mock);
        $stack->push(GuzzleMiddleware::redirect());
        $client = new GuzzleClient(['handler' => $stack]);

        $this->instance(GuzzleClient::class, $client);

        $schedule = app()->make(StatusCron::class);
        $schedule();

        $this->assertDatabaseHas('dirs_status', [
            'dir_id' => $dir->id,
            'attempts' => 1
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::STATUS_INACTIVE
        ]);
    }

    public function testStatusUnknownParkedDomainQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredUrl()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://parked-domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        DirStatus::makeFactory()->for($dir)->create();

        Config::set('idir.dir.status.max_attempts', 1);
        Config::set('idir.dir.status.parked_domains', []);

        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_FOUND, [
                'Location' => 'https://gzermplatz.dasdasdasd.pl/redir.php?panel=Market_Auction&params=id%3D2493603&type=auction&id=2493603&medium=direct:direct'
            ]),
            new GuzzleResponse(HttpResponse::HTTP_FOUND, [
                'Location' => 'https://www.dasdasdas.pl/aukcja/2493603/?_track=504ea78ba428635f7787e4f49c326f88',
            ]),
            new GuzzleResponse(HttpResponse::HTTP_OK)
        ]);

        $stack = new HandlerStack($mock);
        $stack->push(GuzzleMiddleware::redirect());
        $client = new GuzzleClient(['handler' => $stack]);

        $this->instance(GuzzleClient::class, $client);

        $schedule = app()->make(StatusCron::class);
        $schedule();

        $this->assertDatabaseHas('dirs_status', [
            'dir_id' => $dir->id,
            'attempts' => 0
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);
    }

    public function testStatusNotFoundQueueJobFailed(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredUrl()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        DirStatus::makeFactory()->for($dir)->create();

        Config::set('idir.dir.status.max_attempts', 1);

        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_NOT_FOUND)
        ]);

        $stack = new HandlerStack($mock);
        $client = new GuzzleClient(['handler' => $stack]);

        $this->instance(GuzzleClient::class, $client);

        $schedule = app()->make(StatusCron::class);
        $schedule();

        $this->assertDatabaseHas('dirs_status', [
            'dir_id' => $dir->id,
            'attempts' => 1
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::STATUS_INACTIVE
        ]);
    }

    public function testQueueJobExceptionFailed(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredUrl()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        DirStatus::makeFactory()->for($dir)->create();

        Config::set('idir.dir.status.max_attempts', 1);

        $mock = new MockHandler([
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);

        $stack = new HandlerStack($mock);
        $client = new GuzzleClient(['handler' => $stack]);

        $this->instance(GuzzleClient::class, $client);

        $schedule = app()->make(StatusCron::class);
        $schedule();

        $this->assertDatabaseHas('dirs_status', [
            'dir_id' => $dir->id,
            'attempts' => 1
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::STATUS_INACTIVE
        ]);
    }

    public function testQueueJobPass(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->requiredUrl()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->statusInactive()->for($group)->create([
            'url' => 'https://domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::STATUS_INACTIVE
        ]);

        DirStatus::makeFactory()->for($dir)->create([
            'attempts' => 10
        ]);

        Config::set('idir.dir.status.max_attempts', 1);

        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_OK)
        ]);

        $stack = new HandlerStack($mock);
        $client = new GuzzleClient(['handler' => $stack]);

        $this->instance(GuzzleClient::class, $client);

        $schedule = app()->make(StatusCron::class);
        $schedule();

        $this->assertDatabaseHas('dirs_status', [
            'dir_id' => $dir->id,
            'attempts' => 0
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);
    }
}
