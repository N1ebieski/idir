<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Translation\Translator as Lang;

class ModeratorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var User
     */
    protected $user;

    /**
     * Undocumented variable
     *
     * @var Collection|null
     */
    protected $dirs;

    /**
     * Undocumented function
     *
     * @param User $user
     * @param Collection $dirs
     */
    public function __construct(User $user, Collection $dirs = null)
    {
        $this->user = $user;

        $this->dirs = $dirs;
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Lang $lang
     * @return void
     */
    public function build(Dir $dir, Lang $lang)
    {
        $dirRepo = $dir->makeRepo();

        return $this->subject($lang->get('idir::dirs.latest'))
            ->to($this->user->email)
            ->with([
                'dirs' => $this->dirs,
                'dirs_inactive_count' => $dirRepo->countInactive(),
                'dirs_reported_count' => $dirRepo->countReported()
            ])
            ->markdown('idir::mails.dir.moderation');
    }
}
