<?php

namespace N1ebieski\IDir\Http\Requests\Web\Payment\Cashbill;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Web\Payment\Interfaces\CompleteRequestInterface;

class CompleteRequest extends FormRequest implements CompleteRequestInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        if ($this->has('userdata')) {
            $userdata = json_decode($this->input('userdata'));

            $this->merge([
                'uuid' => $userdata->uuid,
                'redirect' => $userdata->redirect
            ]);

            App::make(Request::class)->merge([
                'uuid' => $this->input('uuid')
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'service' => 'bail|required|string|in:' . Config::get("services.cashbill.transfer.service"),
            'orderid' => 'bail|required|string',
            'amount' => 'bail|required|numeric|between:0,99999.99',
            'userdata' => 'bail|required|json',
            'uuid' => 'bail|required|uuid',
            'status' => 'bail|required|in:ok,err',
            'sign' => 'bail|required|string',
            'redirect' => [
                'bail',
                'nullable',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/'
            ]
        ];
    }
}
