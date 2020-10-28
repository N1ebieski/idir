<?php

namespace N1ebieski\IDir\Http\Requests\Admin\Field;

use Illuminate\Foundation\Http\FormRequest;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Contracts\Config\Repository as Config;

class GusRequest extends FormRequest
{
    /**
     * Undocumented variable
     *
     * @var MigrationUtil
     */
    protected $migrationUtil;

    /**
     * Undocumented variable
     *
     * @var Config
     */
    protected $config;

    /**
     * Undocumented function
     *
     * @param MigrationUtil $migrationUtil
     */
    public function __construct(MigrationUtil $migrationUtil, Config $config)
    {
        parent::__construct();

        $this->migrationUtil = $migrationUtil;

        $this->config = $config;
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
