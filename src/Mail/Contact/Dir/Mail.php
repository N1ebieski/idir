<?php

namespace N1ebieski\IDir\Mail\Contact\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\ICore\Mail\Contact\Mail as BaseMail;

/**
 * [Mail description]
 */
class Mail extends BaseMail
{
    /**
     * [protected description]
     * @var Dir
     */
    protected $dir;

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $email;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Dir $dir
     * @param Lang $lang
     * @param URL $url
     * @param Config $config
     */
    public function __construct(
        Request $request,
        Dir $dir,
        Lang $lang,
        URL $url,
        Config $config
    ) {
        parent::__construct($request, $lang, $url, $config);

        $this->dir = $dir;

        $this->email = $this->dir->user->email;
    }

    /**
     * [subcopy description]
     * @return string [description]
     */
    protected function subcopy() : string
    {
        return $this->lang->get('icore::contact.subcopy.form', [
            'url' => $this->url->route('web.dir.show', [$this->dir->slug])
        ]);
    }
}
