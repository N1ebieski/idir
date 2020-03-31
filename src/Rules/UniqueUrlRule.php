<?php

namespace N1ebieski\IDir\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Contracts\Translation\Translator as Lang;
use Illuminate\Database\DatabaseManager as DB;

/**
 * [UniqueUrl description]
 */
class UniqueUrlRule implements Rule
{
    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented variable
     *
     * @var Lang
     */
    protected $lang;

    /**
     * [private description]
     * @var string
     */
    protected $table;

    /**
     * [protected description]
     * @var string
     */
    protected $column;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $ignore;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected array $begins = ['http://', 'http://www.', 'https://', 'https://www.'];

    /**
     * Undocumented function
     *
     * @param DB $db
     * @param string $table
     * @param string $column
     * @param integer $ignore
     */
    public function __construct(DB $db, Lang $lang, string $table, string $column, int $ignore = null)
    {
        $this->db = $db;
        $this->lang = $lang;

        $this->table = $table;
        $this->column = $column;
        $this->ignore = $ignore;
    }

    /**
     * [validate description]
     * @param  [type] $attribute  [description]
     * @param  [type] $value      [description]
     * @param  [type] $parameters [description]
     * @param  [type] $validator  [description]
     * @return [type]             [description]
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        return $this->passes($attribute, $value);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $url = str_replace('www.', '', parse_url($value, PHP_URL_HOST));

        return $this->db->table($this->table)
            ->where(function (Builder $query) use ($url) {
                foreach ($this->begins as $begin) {
                    $query->where('url', '=', $begin . $url, 'or');
                }
            })
            ->when($this->ignore !== null, function (Builder $query) {
                $query->where('id', '<>', $this->ignore);
            })
            ->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->lang->get('validation.unique');
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function __toString() : string
    {
        return 'UniqueUrl';
    }
}
