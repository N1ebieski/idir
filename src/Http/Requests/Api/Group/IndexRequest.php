<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Http\Requests\Api\Group;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\IDir\ValueObjects\Group\Visible;

class IndexRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paginate = Config::get('database.paginate');

        return [
            'page' => 'integer',
            'filter.except' => 'bail|nullable|array',
            'filter.except.*' => 'bail|integer',
            'filter.search' => 'bail|nullable|string|min:3|max:255',
            'filter.visible' => [
                'bail',
                'nullable',
                'integer',
                'in:1' . (
                    optional($this->user())->can('admin.groups.view') ? ',0' : null
                )
            ],
            'filter.orderby' => [
                'bail',
                'nullable',
                'in:created_at|asc,created_at|desc,updated_at|asc,updated_at|desc,name|asc,name|desc,position|asc,position|desc'
            ],
            'filter.paginate' => [
                'bail',
                'nullable',
                'integer',
                Rule::in([$paginate, ($paginate * 2), ($paginate * 4)])
            ]
        ];
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'page' => [
                'example' => 1
            ],
            'filter.except.*' => [
                'description' => 'Array containing IDs, excluding records from the list.',
                'example' => []
            ],
            'filter.search' => [
                'description' => 'Search by keyword.',
                'example' => ''
            ],
            'filter.visible' => [
                'description' => sprintf(
                    'Must be one of %1$s or %2$s (available only for admin.groups.view)',
                    Visible::ACTIVE,
                    Visible::INACTIVE
                ),
                'example' => Visible::ACTIVE
            ],
            'filter.orderby' => [
                'description' => 'Sorting the result list.',
                'example' => ''
            ],
            'filter.paginate' => [
                'description' => 'Number of records in the list.',
                'example' => ''
            ]
        ];
    }
}
