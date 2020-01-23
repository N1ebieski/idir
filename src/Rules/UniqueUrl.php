<?php

namespace N1ebieski\IDir\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * [UniqueUrl description]
 */
class UniqueUrl implements Rule
{
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
     * Undocumented function
     *
     * @param string $table
     * @param string $column
     */
    public function __construct(string $table, string $column, int $ignore = null)
    {
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

        return DB::table($this->table)
            ->where(function($query) use ($url) {
                return $query->whereRaw("MATCH (url) AGAINST (? IN BOOLEAN MODE)", [
                    '+"/' . $url . '"'
                ])
                ->orWhereRaw("MATCH (url) AGAINST (? IN BOOLEAN MODE)", [
                    '+"/www.' . $url . '"'
                ]);
            })
            ->when($this->ignore !== null, function($query) {
                $query->where('id', '<>', $this->ignore);
            })->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.unique');
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
