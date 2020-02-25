<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;

class CompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var Dir
     */
    public $dir;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(trans('idir::dirs.mail.completed.title'))
            ->from(config('mail.from.address'))
            ->to($this->dir->user->email)
            ->with([
                'result' => $this->result()
            ])
            ->markdown('idir::mails.dir.completed');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function result() : string
    {
        if ($this->dir->group->alt_id === null) {
            return trans('idir::dirs.mail.completed.deactivation');
        }

        return trans('idir::dirs.mail.completed.alt', [
            'alt_group' => $this->dir->group->alt->name
        ]);
    }
}
