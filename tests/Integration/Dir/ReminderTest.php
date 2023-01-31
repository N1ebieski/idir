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

use Carbon\Carbon;
use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Price;
use Illuminate\Support\Facades\Mail;
use N1ebieski\IDir\Mail\Dir\ReminderMail;
use N1ebieski\IDir\Crons\Dir\ReminderCron;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReminderTest extends TestCase
{
    use DatabaseTransactions;

    public function testQueueJob(): void
    {
        /** @var Group */
        $group = Group::makeFactory()->applyAltDeactivation()->create();

        Price::makeFactory()->seasonal()->for($group)->create();

        /** @var Dir */
        $dir = Dir::makeFactory()->withUser()->paidSeasonal()->active()->for($group)->create([
            'privileged_to' => Carbon::now()->subDays(14)
        ]);

        Mail::fake();

        $this->assertTrue($dir->privileged_to !== null);

        $schedule = app()->make(ReminderCron::class);
        $schedule();

        Mail::assertSent(ReminderMail::class, function (ReminderMail $mail) use ($dir) {
            $mail->build();

            /** @var User */
            $user = $dir->user;

            return $mail->hasTo($user->email);
        });
    }
}
