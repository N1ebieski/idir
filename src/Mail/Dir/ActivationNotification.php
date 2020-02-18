<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;

/**
 * [ActivationNotification description]
 */
class ActivationNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

    /**
     * [__construct description]
     * @param Dir    $dir    [description]
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build() : self
    {
        return $this->subject(trans('idir::dirs.success.update_status.status_1'))
            ->from(config('mail.from.address'))
            ->to($this->dir->user->email)
            ->markdown('idir::mails.dir.activation');
    }
}
