<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Dir;

use Carbon\Carbon;
use Tests\TestCase;
use GuzzleHttp\HandlerStack;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Crons\Dir\StatusCron;
use N1ebieski\IDir\Mail\Dir\ReminderMail;
use N1ebieski\IDir\Crons\Dir\BacklinkCron;
use N1ebieski\IDir\Crons\Dir\ReminderCron;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use N1ebieski\IDir\Crons\Dir\CompletedCron;
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
        $group = factory(Group::class)->states(['apply_alt_deactivation'])->create();

        $price = factory(Price::class)->states(['seasonal'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'paid_seasonal', 'active'])
            ->create([
                'group_id' => $group->id,
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
        $group = factory(Group::class)->states(['apply_alt_deactivation'])->create();

        $price = factory(Price::class)->states(['seasonal'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'paid_seasonal', 'active'])
            ->create([
                'group_id' => $group->id,
                'privileged_to' => Carbon::now()->subDays(14)
            ]);

        Mail::fake();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 1,
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
            'status' => 2,
            'group_id' => $group->id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testCompletedAltGroupQueueJob()
    {
        $group = factory(Group::class)->states(['apply_alt_group'])->create();

        $price = factory(Price::class)->states(['seasonal'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'paid_seasonal', 'active'])
            ->create([
                'group_id' => $group->id,
                'privileged_at' => null,
                'privileged_to' => null
            ]);

        Mail::fake();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 1,
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
            'status' => 1,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testCompletedAltGroupRemoveCatsQueueJob()
    {
        $group = factory(Group::class)->states(['apply_alt_group'])->create([
            'max_cats' => 5
        ]);

        $price = factory(Price::class)->states(['seasonal'])->make();
        $price->group()->associate($group)->save();

        $categories = factory(Category::class, 5)->states(['active'])->create();

        $dir = factory(Dir::class)->states(['with_user', 'paid_seasonal', 'active'])
            ->create([
                'group_id' => $group->id,
                'privileged_at' => null,
                'privileged_to' => null
            ]);

        $dir->categories()->attach($categories->pluck('id')->toArray());

        Mail::fake();

        $this->assertEquals($dir->categories->count(), 5);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 1,
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
            'status' => 1,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testCompletedAltGroupInfinitePrivilegedToQueueJobFail()
    {
        $group = factory(Group::class)->states(['apply_alt_group'])->create();

        $price = factory(Price::class)->states(['seasonal'])->make();
        $price->group()->associate($group)->save();

        $dir = factory(Dir::class)->states(['with_user', 'paid_seasonal', 'active'])
            ->create([
                'group_id' => $group->id,
                'privileged_to' => null
            ]);

        Mail::fake();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 1,
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
            'status' => 1,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testDeactivateByBacklinkQueueJob()
    {
        $group = factory(Group::class)->states(['required_backlink'])->create();

        $link = factory(Link::class)->states('backlink')->create();

        $dir = factory(Dir::class)->states(['with_user', 'active'])
            ->create([
                'group_id' => $group->id,
            ]);

        $dirBacklink = $dir->backlink()->make(['url' => 'http://dadadad.pl']);
        $dirBacklink->link()->associate($link);
        $dirBacklink->dir()->associate($dir);
        $dirBacklink->save();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 1,
            'group_id' => $group->id,
        ]);

        Config::set('idir.dir.backlink.max_attempts', 1);

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl', ['verify' => false])->andReturn(
                new GuzzleResponse(200, [], 'dadasdasd sdsajdhjashdj')
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
            'status' => 3,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseHas('dirs_backlinks', [
            'id' => $dirBacklink->id,
            'attempts' => 1
        ]);
    }

    public function testActivateByBacklinkQueueJob()
    {
        $group = factory(Group::class)->states(['required_backlink'])->create();

        $link = factory(Link::class)->states('backlink')->create();

        $dir = factory(Dir::class)->states(['with_user', 'backlink_inactive'])
            ->create([
                'group_id' => $group->id,
            ]);

        $dirBacklink = $dir->backlink()->make([
            'url' => 'http://dadadad.pl',
            'attempts' => 5
        ]);
        $dirBacklink->link()->associate($link);
        $dirBacklink->dir()->associate($dir);
        $dirBacklink->save();

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => 3,
            'group_id' => $group->id,
        ]);

        Config::set('idir.dir.backlink.max_attempts', 1);

        $this->mock(GuzzleClient::class, function ($mock) use ($link) {
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl', ['verify' => false])->andReturn(
                new GuzzleResponse(200, [], '<a href="' . $link->url . '">dadasdasd</a>')
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
            'status' => 1,
            'group_id' => $group->id
        ]);

        $this->assertDatabaseHas('dirs_backlinks', [
            'id' => $dirBacklink->id,
            'attempts' => 0
        ]);
    }

    public function testStatusKnownParkedDomainQueueJob()
    {
        $group = factory(Group::class)->states(['required_url'])->create();

        $dir = factory(Dir::class)->states(['with_user', 'active'])
            ->create([
                'group_id' => $group->id,
                'url' => 'https://parked-domain.pl'
            ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::ACTIVE
        ]);

        $dirStatus = $dir->status()->make();
        $dirStatus->dir()->associate($dir);
        $dirStatus->save();

        Config::set('idir.dir.status.max_attempts', 1);
        Config::set('idir.dir.status.parked_domains', [
            'aftermarket.pl',
            'blablabla.pl'
        ]);

        $mock = new MockHandler([
            new GuzzleResponse(302, [
                'Location' => 'https://gzermplatz.aftermarket.pl/redir.php?panel=Market_Auction&params=id%3D2493603&type=auction&id=2493603&medium=direct:direct'
            ]),
            new GuzzleResponse(302, [
                'Location' => 'https://www.aftermarket.pl/aukcja/2493603/?_track=504ea78ba428635f7787e4f49c326f88',
            ]),
            new GuzzleResponse(200)
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
            'status' => Dir::STATUS_INACTIVE
        ]);
    }

    public function testStatusUnknownParkedDomainQueueJob()
    {
        $group = factory(Group::class)->states(['required_url'])->create();

        $dir = factory(Dir::class)->states(['with_user', 'active'])
            ->create([
                'group_id' => $group->id,
                'url' => 'https://parked-domain.pl'
            ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::ACTIVE
        ]);

        $dirStatus = $dir->status()->make();
        $dirStatus->dir()->associate($dir);
        $dirStatus->save();

        Config::set('idir.dir.status.max_attempts', 1);
        Config::set('idir.dir.status.parked_domains', []);

        $mock = new MockHandler([
            new GuzzleResponse(302, [
                'Location' => 'https://gzermplatz.dasdasdasd.pl/redir.php?panel=Market_Auction&params=id%3D2493603&type=auction&id=2493603&medium=direct:direct'
            ]),
            new GuzzleResponse(302, [
                'Location' => 'https://www.dasdasdas.pl/aukcja/2493603/?_track=504ea78ba428635f7787e4f49c326f88',
            ]),
            new GuzzleResponse(200)
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
            'status' => Dir::ACTIVE
        ]);
    }

    public function testStatusQueueJobFailed()
    {
        $group = factory(Group::class)->states(['required_url'])->create();

        $dir = factory(Dir::class)->states(['with_user', 'active'])
            ->create([
                'group_id' => $group->id,
                'url' => 'https://domain.pl'
            ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::ACTIVE
        ]);

        $dirStatus = $dir->status()->make();
        $dirStatus->dir()->associate($dir);
        $dirStatus->save();

        Config::set('idir.dir.status.max_attempts', 1);

        $mock = new MockHandler([
            new GuzzleResponse(404)
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
            'status' => Dir::STATUS_INACTIVE
        ]);
    }

    public function testStatusQueueJobPass()
    {
        $group = factory(Group::class)->states(['required_url'])->create();

        $dir = factory(Dir::class)->states(['with_user', 'status_inactive'])
            ->create([
                'group_id' => $group->id,
                'url' => 'https://domain.pl'
            ]);

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Dir::STATUS_INACTIVE
        ]);

        $dirStatus = $dir->status()->make([
            'attempts' => 10
        ]);
        $dirStatus->dir()->associate($dir);
        $dirStatus->save();

        Config::set('idir.dir.status.max_attempts', 1);

        $mock = new MockHandler([
            new GuzzleResponse(200)
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
            'status' => Dir::ACTIVE
        ]);
    }
}
