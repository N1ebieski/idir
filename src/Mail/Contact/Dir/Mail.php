<?php

namespace N1ebieski\IDir\Mail\Contact\Dir;

use Illuminate\Http\Request;
use N1ebieski\ICore\Mail\Contact\Mail as BaseMail;
use N1ebieski\IDir\Models\Dir;

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
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request, Dir $dir)
    {
        parent::__construct($request);

        $this->dir = $dir;

        $this->email = $this->dir->user->email ?? null;
    }

    /**
     * [subcopy description]
     * @return string [description]
     */
    protected function subcopy() : string
    {
        return trans('icore::contact.subcopy.form', [
            'url' => route('web.dir.show', [$this->dir->slug])
        ]);
    }
}
