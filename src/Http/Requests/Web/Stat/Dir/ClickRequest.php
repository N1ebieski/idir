<?php

namespace N1ebieski\IDir\Http\Requests\Web\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Utils\MigrationUtil;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Dir $dir_cache
 */
class ClickRequest extends FormRequest
{
    /**
     * Undocumented variable
     *
     * @var MigrationUtil
     */
    protected $migrationUtil;

    /**
     * Undocumented function
     *
     * @param MigrationUtil $migrationUtil
     */
    public function __construct(MigrationUtil $migrationUtil)
    {
        parent::__construct();

        $this->migrationUtil = $migrationUtil;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->dir_cache->status->isActive()
            && $this->dir_cache->url->isUrl()
            && $this->migrationUtil->contains('create_stats_table');
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
