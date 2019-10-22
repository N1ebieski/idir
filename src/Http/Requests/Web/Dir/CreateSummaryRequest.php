<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use N1ebieski\IDir\Http\Requests\Web\Dir\StoreFormRequest;

class CreateSummaryRequest extends StoreFormRequest
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
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        $url = $this->redirector->getUrlGenerator();

        return $url->route('web.dir.create_form', [$this->group_dir_available->id]);
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if (!$this->old('payment_type')) {
            session()->put(
                '_old_input.payment_type', $this->group_dir_available->prices->sortByDesc('type')->first()->type
            );
        }

        if ($this->old('payment_code_sms')) {
            session()->flash('_old_input.payment_code_sms_model',
                $this->group_dir_available->prices->where('id', old('payment_code_sms'))->first()
            );
        }

        if ($this->old('payment_code_transfer')) {
            session()->flash('_old_input.payment_code_transfer_model',
                $this->group_dir_available->prices->where('id', old('payment_code_transfer'))->first()
            );
        }

        if ($this->session()->has('dir')) {
            $this->merge($this->session()->get('dir'));
        }

        parent::prepareForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }
}
