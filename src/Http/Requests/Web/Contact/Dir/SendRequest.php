<?php

namespace N1ebieski\IDir\Http\Requests\Web\Contact\Dir;

use N1ebieski\ICore\Http\Requests\Web\Contact\SendRequest as BaseSendRequest;

class SendRequest extends BaseSendRequest
{
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
