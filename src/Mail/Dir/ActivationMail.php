<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * [ActivationNotification description]
 */
class ActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * [public description]
     * @var Dir
     */
    public $dir;

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
    protected string $email;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Lang $lang
     * @param Config $config
     */
    public function __construct(Dir $dir, Lang $lang, Config $config)
    {
        $this->dir = $dir;

        $this->lang = $lang;

        $this->email = $config->get('mail.from.address');
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function build() : self
    {
        return $this->subject($this->lang->get('idir::dirs.success.update_status.'.Dir::ACTIVE))
            ->from($this->email)
            ->to($this->dir->user->email)
            ->markdown('idir::mails.dir.activation');
    }
}
