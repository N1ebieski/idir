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

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Translation\Translator as Lang;

class ActivationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Lang $lang
     */
    public function __construct(
        public Dir $dir,
        protected Lang $lang
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function build(): self
    {
        /** @var User */
        $user = $this->dir->user;

        return $this->subject($this->lang->get('idir::dirs.success.update_status.' . Status::ACTIVE))
            ->to($user->email)
            ->markdown('idir::mails.dir.activation');
    }
}
