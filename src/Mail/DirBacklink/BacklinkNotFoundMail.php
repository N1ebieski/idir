<?php

namespace N1ebieski\IDir\Mail\DirBacklink;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Translation\Translator as Lang;

class BacklinkNotFoundMail extends Mailable
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
     * Undocumented variable
     *
     * @var string
     */
    protected $email;

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
        $this->dirBacklink->load(['link', 'dir', 'dir.user']);

        return $this->subject($this->lang->get('idir::backlinks.not_found'))
            ->to($this->dirBacklink->dir->user->email)
            ->markdown('idir::mails.backlink.not_found');
    }
}
