<?php

namespace N1ebieski\IDir\Http\Requests\Api\Payment\Interfaces;

interface VerifyRequestStrategy
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool;
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array;
}
