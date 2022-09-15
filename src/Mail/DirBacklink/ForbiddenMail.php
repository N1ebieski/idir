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

namespace N1ebieski\IDir\Mail\DirBacklink;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\User;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Translation\Translator as Lang;

class ForbiddenMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     * @param Lang $lang
     */
    public function __construct(
        public DirBacklink $dirBacklink,
        protected Lang $lang
    ) {
        //
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build(): self
    {
        $this->dirBacklink->load(['dir', 'dir.user', 'dir.group']);

        /** @var User */
        $user = $this->dirBacklink->dir->user;

        return $this->subject($this->lang->get('idir::backlinks.mail.forbidden.title'))
            ->to($user->email)
            ->with([
                'greeting' => $this->greeting()
            ])
            ->markdown('idir::mails.backlink.forbidden');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function greeting(): string
    {
        /** @var User */
        $user = $this->dirBacklink->dir->user;

        return $this->lang->get('icore::auth.hello') . ' ' . $user->name . '!';
    }
}
