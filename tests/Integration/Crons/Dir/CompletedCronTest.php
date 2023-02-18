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

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mail\Dir\CompletedMail;
use N1ebieski\IDir\Crons\Dir\CompletedCron;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompletedCronTest extends TestCase
{
    use DatabaseTransactions;

    public function testDeactivationQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->applyAltDeactivation()->create();

        Price::makeFactory()->seasonal()->for($group)->create();

        /** @var Dir */
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

        Mail::assertSent(CompletedMail::class, function (CompletedMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::PAYMENT_INACTIVE,
            'group_id' => $group->id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testAltGroupQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->applyAltGroup()->create();

        Price::makeFactory()->seasonal()->for($group)->create();

        /** @var Dir */
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

        Mail::assertSent(CompletedMail::class, function (CompletedMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }

    public function testAltGroupRemoveCatsQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->applyAltGroup()->create([
            'max_cats' => 5
        ]);

        Price::makeFactory()->seasonal()->for($group)->create();

        /** @var Collection<Category> */
        $categories = Category::makeFactory()->count(5)->active()->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)
            ->hasAttached($categories, [], 'categories')
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

        Mail::assertSent(CompletedMail::class, function (CompletedMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
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

    public function testAltGroupInfinitePrivilegedToQueueJobFail(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->applyAltGroup()->create();

        Price::makeFactory()->seasonal()->for($group)->create();

        /** @var Dir */
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

        Mail::assertNotSent(CompletedMail::class, function (CompletedMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseMissing('dirs', [
            'id' => $dir->id,
            'status' => Status::ACTIVE,
            'group_id' => $group->alt_id,
            'privileged_at' => null,
            'privileged_to' => null
        ]);
    }
}
