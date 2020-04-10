<?php

namespace N1ebieski\IDir\Mail\DirBacklink;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Config\Repository as Config;

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
     * @param Config $config
     */
    public function __construct(DirBacklink $dirBacklink, Lang $lang, Config $config)
    {
        $this->dirBacklink = $dirBacklink;

        $this->lang = $lang;

        $this->email = $config->get('mail.from.address');
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build() : self
    {
        $this->dirBacklink->load(['link', 'dir', 'dir.user']);

        return $this->subject($this->lang->get('idir::backlinks.not_found'))
            ->from($this->email)
            ->to($this->dirBacklink->dir->user->email)
            ->markdown('idir::mails.backlink.not_found');
    }
}
