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

namespace N1ebieski\IDir\Rules;

use Illuminate\Database\Query\Builder;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\DatabaseManager as DB;
use Illuminate\Contracts\Translation\Translator as Lang;

class UniqueUrlRule implements Rule
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $begins = ['http://', 'http://www.', 'https://', 'https://www.'];

    /**
     *
     * @param DB $db
     * @param Lang $lang
     * @param string $table
     * @param string $column
     * @param null|int $ignore
     * @return void
     */
    public function __construct(
        protected DB $db,
        protected Lang $lang,
        protected string $table,
        protected string $column,
        protected ?int $ignore = null
    ) {
        //
    }

    /**
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $parameters
     * @param mixed $validator
     * @return bool
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
        $url = preg_replace('/^www\./', '', parse_url($value, PHP_URL_HOST) ?: '');

        return $this->db->table($this->table)
            ->where(function (Builder $query) use ($url) {
                foreach ($this->begins as $begin) {
                    $query->where($this->column, '=', $begin . $url, 'or');
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
    public function __toString(): string
    {
        return 'UniqueUrl';
    }
}
