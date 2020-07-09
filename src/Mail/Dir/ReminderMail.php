<?php

namespace N1ebieski\IDir\Mail\Dir;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator as Lang;

class ReminderMail extends Mailable implements ShouldQueue
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
     * Undocumented function
     *
     * @param Dir $dir
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * Undocumented function
     *
     * @param Lang $lang
     * @return void
     */
    public function build(Lang $lang)
    {
        $this->lang = $lang;

        return $this->subject($this->lang->get('idir::dirs.mail.reminder.title'))
            ->to($this->dir->user->email)
            ->with([
                'result' => $this->result()
            ])
            ->markdown('idir::mails.dir.reminder');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function result() : string
    {
        if ($this->dir->group->alt_id === null) {
            return $this->lang->get('idir::dirs.mail.reminder.deactivation', [
                'days' => $this->dir->privileged_to_diff
            ]);
        }

        return $this->lang->get('idir::dirs.mail.reminder.alt', [
            'days' => $this->dir->privileged_to_diff,
            'alt_group' => $this->dir->group->alt->name
        ]);
    }
}
