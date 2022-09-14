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

namespace N1ebieski\IDir\Jobs\Dir;

use Throwable;
use Illuminate\Bus\Queueable;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Mail\Dir\ReminderMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Container\Container as App;

class ReminderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @param App $app
     * @param Mailer $mailer
     * @return void
     */
    public function handle(App $app, Mailer $mailer)
    {
        $this->app = $app;
        $this->mailer = $mailer;

        if (!$this->verify()) {
            return;
        }

        $this->mailer->send(
            $this->app->make(ReminderMail::class, ['dir' => $this->dir])
        );
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function verify(): bool
    {
        return optional($this->dir->user)->email
            && optional($this->dir->user)->hasPermissionTo('web.dirs.notification');
    }

    /**
     * The job failed to process.
     *
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        //
    }
}
