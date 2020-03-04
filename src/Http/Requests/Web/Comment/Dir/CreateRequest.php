<?php

namespace N1ebieski\IDir\Http\Requests\Web\Comment\Dir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use N1ebieski\IDir\Models\Comment\Dir\Comment;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // if ((bool)$this->post->comment === false) {
        //     abort(403, 'Adding comments has been disabled for this post.');
        // }

        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->has('parent_id') || $this->get('parent_id') == 0) {
            $this->merge([
                'parent_id' => null
            ]);
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
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(function ($query) {
                    $query->where('status', Comment::ACTIVE);
                }),
            ]
        ];
    }
}
