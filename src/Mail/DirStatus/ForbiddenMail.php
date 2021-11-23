<?php

namespace N1ebieski\IDir\Mail\DirStatus;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\DirStatus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Translation\Translator as Lang;

class ForbiddenMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * [public description]
     * @var DirStatus
     */
    public $dirStatus;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param DirStatus $dirStatus
     * @param Lang $lang
     */
    public function __construct(DirStatus $dirStatus, Lang $lang)
    {
        $this->dirStatus = $dirStatus;

        $this->lang = $lang;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build(): self
    {
        $this->dirStatus->load(['dir', 'dir.user', 'dir.group']);

        return $this->subject($this->lang->get('idir::status.mail.forbidden.title'))
            ->to($this->dirStatus->dir->user->email)
            ->with([
                'greeting' => $this->greeting()
            ])
            ->markdown('idir::mails.status.forbidden');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function greeting(): string
    {
        return $this->lang->get('icore::auth.hello') . ' ' . $this->dirStatus->dir->user->name . '!';
    }
}
