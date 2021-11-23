<?php

namespace N1ebieski\IDir\Mail\DirBacklink;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Translation\Translator as Lang;

class ForbiddenMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * [public description]
     * @var DirBacklink
     */
    public $dirBacklink;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * Undocumented function
     *
     * @param DirBacklink $dirBacklink
     * @param Lang $lang
     */
    public function __construct(DirBacklink $dirBacklink, Lang $lang)
    {
        $this->dirBacklink = $dirBacklink;

        $this->lang = $lang;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build(): self
    {
        $this->dirBacklink->load(['dir', 'dir.user', 'dir.group']);

        return $this->subject($this->lang->get('idir::backlinks.mail.forbidden.title'))
            ->to($this->dirBacklink->dir->user->email)
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
        return $this->lang->get('icore::auth.hello') . ' ' . $this->dirBacklink->dir->user->name . '!';
    }
}
