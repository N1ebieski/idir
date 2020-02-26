<?php

namespace N1ebieski\IDir\Mail\DirBacklink;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;

/**
 * [BacklinkNotFound description]
 */
class BacklinkNotFoundMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * Create a new event instance.
     *
     * @param DirBacklink $dirBacklink
     * @return void
     */
    public function __construct(DirBacklink $dirBacklink)
    {
        $this->dirBacklink = $dirBacklink;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build() : self
    {
        $this->dirBacklink->load(['link', 'dir', 'dir.user']);

        return $this->subject(trans('idir::backlinks.not_found'))
            ->from(config('mail.from.address'))
            ->to($this->dirBacklink->dir->user->email)
            ->markdown('idir::mails.backlink.not_found')
            ->with(['dirBacklink' => $this->dirBacklink]);
    }
}
