<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * [VerifyRequest description]
 */
class VerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * [prepareForValidation description]
     * @return [type] [description]
     */
    public function prepareForValidation() {
        if ($this->has('userdata')) {
            $userdata = json_decode($this->input('userdata'));

            $this->merge([
                'id' => $userdata->id,
                'redirect' => $userdata->redirect ?? 'web.profile.edit_dir'
            ]);
        }

        app(Request::class)->merge([
            'logs' => $this->all()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service' => 'bail|required|string|in:' . config("services.cashbill.transfer.service"),
            'orderid' => 'bail|required|string',
            'amount' => 'bail|required|numeric|between:0,9999.99',
            'userdata' => 'bail|required|json',
            'status' => 'bail|required|in:ok,err',
            'sign' => 'bail|required|string',
            'id' => 'bail|required|integer',
            'redirect' => 'bail|nullable|string|in:admin.dir.index,web.profile.edit_dir'
        ];
    }
}
