<?php

namespace N1ebieski\IDir\Http\Requests\Api\Payment\PayPal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces\VerifyRequestInterface;

class VerifyRequest extends FormRequest implements VerifyRequestInterface
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
     * [prepareForValidation description]
     * @return [type] [description]
     */
    public function prepareForValidation()
    {
        $this->merge([
            'uuid' => $this->input('invoice')
        ]);

        App::make(Request::class)->merge([
            'uuid' => $this->input('invoice'),
            'logs' => $this->all()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'invoice' => 'bail|required|uuid',
            'uuid' => 'bail|required|uuid'
        ];
    }
}
