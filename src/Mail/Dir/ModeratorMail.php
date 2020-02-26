<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;

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
     * Build the message.
     *
     * @return $this
     */
    public function build(Dir $dir)
    {
        $dirRepo = $dir->makeRepo();

        return $this->subject(trans('idir::dirs.latest'))
            ->from(config('mail.from.address'))
            ->to($this->user->email)
            ->with([
                'dirs' => $this->dirs,
                'dirs_inactive_count' => $dirRepo->countInactive(),
                'dirs_reported_count' => $dirRepo->countReported()
            ])
            ->markdown('idir::mails.dir.moderation');
    }
}
