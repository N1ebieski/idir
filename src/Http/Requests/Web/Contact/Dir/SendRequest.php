<?php

namespace N1ebieski\IDir\Http\Requests\Web\Contact\Dir;

use N1ebieski\ICore\Http\Requests\Web\Contact\SendRequest as BaseSendRequest;
use N1ebieski\ICore\View\Components\CaptchaComponent as Captcha;

class SendRequest extends BaseSendRequest
{
    /**
     * Undocumented function
     *
     * @param Captcha $captcha
     */
    public function __construct(Captcha $captcha)
    {
        parent::__construct($captcha);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->isActive() && isset($this->dir->user->email);
    }
}
