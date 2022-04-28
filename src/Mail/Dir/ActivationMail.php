<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Translation\Translator as Lang;

class ActivationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

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
     * Undocumented function
     *
     * @param Dir $dir
     * @param Lang $lang
     */
    public function __construct(Dir $dir, Lang $lang)
    {
        $this->dir = $dir;

        $this->lang = $lang;
    }

    /**
     * Undocumented function
     *
     * @return self
     */
    public function build(): self
    {
        return $this->subject($this->lang->get('idir::dirs.success.update_status.' . Status::ACTIVE))
            ->to($this->dir->user->email)
            ->markdown('idir::mails.dir.activation');
    }
}
