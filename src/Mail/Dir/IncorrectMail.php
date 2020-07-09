<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Translation\Translator as Lang;

class IncorrectMail extends Mailable
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
    protected $email;

    /**
     * [public description]
     * @var string|null
     */
    public $reason;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Lang $lang
     * @param string|null $reason
     */
    public function __construct(Dir $dir, Lang $lang, string $reason = null)
    {
        $this->dir = $dir;

        $this->lang = $lang;

        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return self
     */
    public function build() : self
    {
        return $this->subject($this->lang->get('idir::dirs.success.update_status.' . $this->dir::INCORRECT_INACTIVE))
            ->to($this->dir->user->email)
            ->markdown('idir::mails.dir.incorrect');
    }
}
