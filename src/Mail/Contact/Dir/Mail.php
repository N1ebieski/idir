<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Mail\Contact\Dir;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;

class Mail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     *
     * @param Dir $dir
     * @param Request $request
     * @param Lang $lang
     * @param URL $url
     * @param Config $config
     * @return void
     */
    public function __construct(
        protected Dir $dir,
        protected Request $request,
        protected Lang $lang,
        protected URL $url,
        protected Config $config
    ) {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /** @var User */
        $user = $this->dir->user;

        return $this->subject($this->request->input('title'))
            ->replyTo($user->email)
            ->to($this->config->get('mail.from.address'))
            ->markdown('icore::mails.contact')
            ->with([
                'subcopy' => $this->subcopy(),
                'content' => $this->request->input('content')
            ]);
    }

    /**
     * [subcopy description]
     * @return string [description]
     */
    protected function subcopy(): string
    {
        return $this->lang->get('icore::contact.subcopy.form', [
            'url' => $this->url->route('web.dir.show', [$this->dir->slug])
        ]);
    }
}
