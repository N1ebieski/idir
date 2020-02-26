<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;

/**
 * [DeleteNotification description]
 */
class DeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

    /**
     * [public description]
     * @var string|null
     */
    public $reason;

    /**
     * [__construct description]
     * @param Dir    $dir    [description]
     * @param string|null $reason [description]
     */
    public function __construct(Dir $dir, string $reason = null)
    {
        $this->dir = $dir;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build() : self
    {
        return $this->subject(trans('idir::dirs.success.destroy'))
            ->from(config('mail.from.address'))
            ->to($this->dir->user->email)
            ->markdown('idir::mails.dir.delete');
    }
}
