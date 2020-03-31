<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Dir;

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\Link;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Crons\Dir\BacklinkCron;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use N1ebieski\IDir\Crons\Dir\CompletedCron;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Mail\DirBacklink\BacklinkNotFoundMail;

/**
 * [DirTest description]
 */
class DirTest extends TestCase
{
    use DatabaseTransactions;

    public function test_completed_deactivation_queue_job()
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

    public function test_completed_alt_group_queue_job()
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

    public function test_completed_alt_group_infinite_privileged_to_queue_job_fail()
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

    public function test_deactivate_by_backlink_queue_job()
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
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl')->andReturn(
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

    public function test_activate_by_backlink_queue_job()
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
            $mock->shouldReceive('request')->with('GET', 'http://dadadad.pl')->andReturn(
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
}
