<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Dir;

use Carbon\Carbon;
use Tests\TestCase;
use GuzzleHttp\HandlerStack;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Models\DirStatus;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\DirBacklink;
use N1ebieski\IDir\Crons\Dir\StatusCron;
use N1ebieski\IDir\Mail\Dir\ReminderMail;
use N1ebieski\IDir\Crons\Dir\BacklinkCron;
use N1ebieski\IDir\Crons\Dir\ReminderCron;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use N1ebieski\IDir\Crons\Dir\CompletedCron;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Http\Response as HttpResponse;
use GuzzleHttp\Middleware as GuzzleMiddleware;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Mail\DirBacklink\BacklinkNotFoundMail;

class DirTest extends TestCase
{
    use DatabaseTransactions;

    public function testReminderQueueJob()
    {
        $group = Group::makeFactory()->applyAltDeactivation()->create();

        $price = Price::makeFactory()->seasonal()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)->create([
            'privileged_to' => Carbon::now()->subDays(14)
        ]);

        Mail::fake();

        $this->assertTrue($dir->privileged_to !== null);

        $schedule = app()->make(ReminderCron::class);
        $schedule();

        Mail::assertSent(ReminderMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
        });
    }

    public function testCompletedDeactivationQueueJob()
    {
        $group = Group::makeFactory()->applyAltDeactivation()->create();

        $price = Price::makeFactory()->seasonal()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)->create([
            'privileged_to' => Carbon::now()->subDays(14)
        ]);

        Mail::fake();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id,
        ]);

        $this->assertTrue($dir->privileged_to !== null);

        $schedule = app()->make(CompletedCron::class);
        $schedule();

        Mail::assertSent(CompletedMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
        });

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::PAYMENT_INACTIVE,
            'group_id' => $group->id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testCompletedAltGroupQueueJob()
    {
        $group = Group::makeFactory()->applyAltGroup()->create();

        $price = Price::makeFactory()->seasonal()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)->create([
            'privileged_at' => null,
            'privileged_to' => null
        ]);

        Mail::fake();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);

        $schedule = app()->make(CompletedCron::class);
        $schedule();

        Mail::assertSent(CompletedMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
        });

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testCompletedAltGroupRemoveCatsQueueJob()
    {
        $group = Group::makeFactory()->applyAltGroup()->create([
            'max_cats' => 5
        ]);

        $price = Price::makeFactory()->seasonal()->for($group)->create();

        $categories = Category::makeFactory()->count(5)->active()->create();

        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)
            ->hasAttached($categories->pluck('id')->toArray(), [], 'categories')
            ->create([
                'privileged_at' => null,
                'privileged_to' => null
            ]);

        Mail::fake();

        $this->assertEquals($dir->categories->count(), 5);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);

        $schedule = app()->make(CompletedCron::class);
        $schedule();

        Mail::assertSent(CompletedMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
        });

        $dir->refresh();

        $this->assertEquals(3, $dir->categories->count());

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testCompletedAltGroupInfinitePrivilegedToQueueJobFail()
    {
        $group = Group::makeFactory()->applyAltGroup()->create();

        $price = Price::makeFactory()->seasonal()->for($group)->create();

        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)->create([
            'privileged_to' => null
        ]);

        Mail::fake();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id,
            'privileged_to' => null
        ]);

        $this->assertTrue($dir->privileged_at !== null);

        $schedule = app()->make(CompletedCron::class);
        $schedule();

        Mail::assertNotSent(CompletedMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
        });

        $this->assertDatabaseMissing('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testDeactivateByBacklinkQueueJob()
    {
        $group = Group::makeFactory()->requiredBacklink()->create();

        $link = Link::makeFactory()->backlink()->create();

        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create();

        $dirBacklink = DirBacklink::makeFactory()->for($link)->for($dir)->create(['url' => 'http://dadadad.pl']);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->id,
        ]);

        Config::set('idir.dir.backlink.max_attempts', 1);

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl', ['verify' => false])->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], 'dadasdasd sdsajdhjashdj')
            );
        });

        Mail::fake();

        $schedule = app()->make(BacklinkCron::class);
        $schedule();

        Mail::assertSent(BacklinkNotFoundMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
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

    public function testActivateByBacklinkQueueJob()
    {
        $group = Group::makeFactory()->requiredBacklink()->create();

        $link = Link::makeFactory()->backlink()->create();

        $dir = Dir::makeFactory()->withUser()->backlinkInactive()->for($group)->create();

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

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl', ['verify' => false])->andReturn(
                new GuzzleResponse(HttpResponse::HTTP_OK, [], '<a href="' . $link->url . '">dadasdasd</a>')
            );
        });

        Mail::fake();

        $schedule = app()->make(BacklinkCron::class);
        $schedule();

        Mail::assertNotSent(BacklinkNotFoundMail::class, function ($mail) use ($dir) {
            $mail->build();

            return $mail->hasTo($dir->user->email);
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

    public function testStatusKnownParkedDomainQueueJob()
    {
        $group = Group::makeFactory()->requiredUrl()->create();

        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://parked-domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        $dirStatus = DirStatus::makeFactory()->for($dir)->create();

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

    public function testStatusUnknownParkedDomainQueueJob()
    {
        $group = Group::makeFactory()->requiredUrl()->create();

        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://parked-domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        $dirStatus = DirStatus::makeFactory()->for($dir)->create();

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

    public function testStatusQueueJobFailed()
    {
        $group = Group::makeFactory()->requiredUrl()->create();

        $dir = Dir::makeFactory()->withUser()->active()->for($group)->create([
            'url' => 'https://domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE
        ]);

        $dirStatus = DirStatus::makeFactory()->for($dir)->create();

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

    public function testStatusQueueJobPass()
    {
        $group = Group::makeFactory()->requiredUrl()->create();

        $dir = Dir::makeFactory()->withUser()->statusInactive()->for($group)->create([
            'url' => 'https://domain.pl'
        ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::STATUS_INACTIVE
        ]);

        $dirStatus = DirStatus::makeFactory()->for($dir)->create([
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
