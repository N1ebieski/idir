<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Translation\Translator as Lang;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->lang->get('idir::dirs.mail.completed.title'))
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
            return $this->lang->get('idir::dirs.mail.completed.deactivation');
        }

        return $this->lang->get('idir::dirs.mail.completed.alt', [
            'alt_group' => $this->dir->group->alt->name
        ]);
    }
}
