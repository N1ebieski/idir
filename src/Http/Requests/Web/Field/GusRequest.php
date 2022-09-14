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

namespace N1ebieski\IDir\Http\Requests\Web\Field;

use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Config\Repository as Config;

class GusRequest extends FormRequest
{
    /**
     *
     * @param MigrationUtil $migrationUtil
     * @param Config $config
     * @return void
     */
    public function __construct(
        protected MigrationUtil $migrationUtil,
        protected Config $config
    ) {
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->migrationUtil->contains('add_gus_entry_to_fields_table')
            && !empty($this->config->get('services.gus.api_key'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'bail|required|string|in:nip,regon,krs',
            'number' => 'bail|required|int|digits_between:7,14'
        ];
    }
}
