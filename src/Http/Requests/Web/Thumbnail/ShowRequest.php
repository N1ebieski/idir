<?php

namespace N1ebieski\IDir\Http\Requests\Web\Thumbnail;

use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response as HttpResponse;

class ShowRequest extends FormRequest
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
     * Undocumented function
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->prepareUrlAttribute();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function prepareUrlAttribute(): void
    {
        if ($this->has('url') && is_string($this->input('url'))) {
            try {
                $url = App::make('crypt.thumbnail')->decryptString($this->input('url'));
            } catch (\Exception $e) {
                App::abort(HttpResponse::HTTP_FORBIDDEN, $e->getMessage());
            }

            $this->merge(['url' => $url]);
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
            'url' => [
                'bail',
                'required',
                'string',
                'regex:/^(https|http):\/\/([\da-z\.-]+)(\.[a-z]{2,6})/',
            ]
        ];
    }
}
