<?php

namespace N1ebieski\IDir\Tests\Feature\Web\Dir;

use Carbon\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Crons\Dir\CompletedCron;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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

        exec('php artisan queue:work --env=testing --daemon --stop-when-empty --tries=3');

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

        exec('php artisan queue:work --env=testing --daemon --stop-when-empty --tries=3');

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

        exec('php artisan queue:work --env=testing --daemon --stop-when-empty --tries=3');

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
}
