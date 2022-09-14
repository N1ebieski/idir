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
use Illuminate\Contracts\Queue\ShouldQueue;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Translation\Translator as Lang;

class ModeratorMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Collection $dirs
     */
    public function __construct(
        protected User $user,
        protected ?Collection $dirs = null
    ) {
        $this->user = $user;

        $this->dirs = $dirs;
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Lang $lang
     * @return self
     */
    public function build(Dir $dir, Lang $lang): self
    {
        $dirRepo = $dir->makeRepo();

        return $this->subject($lang->get('idir::dirs.latest'))
            ->to($this->user->email)
            ->with([
                'dirs' => $this->dirs,
                'dirs_inactive_count' => $dirRepo->countByStatus()
                    ->firstWhere('status', Status::INACTIVE)->count ?? 0,
                'dirs_reported_count' => $dirRepo->countReported()
            ])
            ->markdown('idir::mails.dir.moderation');
    }
}
