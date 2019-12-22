<?php

namespace N1ebieski\IDir\Http\Requests\Web\Dir;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [EditRenewRequest description]
 */
class EditRenewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir->group->isPublic() && $this->dir->isRenew();
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() : void
    {
        if ($this->dir->group->prices->isNotEmpty()) {
            $this->preparePaymentTypeOldAttribute();

            $this->preparePaymentCodeSmsModelOldAttribute();

            $this->preparePaymentCodeTransferModelOldAttribute();
        }
    }

    /**
     * [preparePaymentTypeOldAttribute description]
     */
    protected function preparePaymentTypeOldAttribute() : void
    {
        if (!$this->old('payment_type')) {
            session()->put(
                '_old_input.payment_type', $this->dir->group->prices->sortByDesc('type')->first()->type
            );
        }
    }

    /**
     * [preparePaymentCodeSmsModelOldAttribute description]
     */
    protected function preparePaymentCodeSmsModelOldAttribute() : void
    {
        if ($this->old('payment_code_sms')) {
            session()->flash('_old_input.payment_code_sms_model',
                $this->dir->group->prices->where('id', old('payment_code_sms'))->first()
            );
        }
    }

    /**
     * [preparePaymentCodeTransferModelOldAttribute description]
     */
    protected function preparePaymentCodeTransferModelOldAttribute() : void
    {
        if ($this->old('payment_code_transfer')) {
            session()->flash('_old_input.payment_code_transfer_model',
                $this->dir->group->prices->where('id', old('payment_code_transfer'))->first()
            );
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
