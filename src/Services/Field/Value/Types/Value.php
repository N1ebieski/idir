<?php

namespace N1ebieski\IDir\Services\Field\Value\Types;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\DatabaseManager as DB;

abstract class Value
{
    /**
     * Undocumented variable
     *
     * @var Field
     */
    protected $field;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented function
     *
     * @param Field $field
     * @param DB $db
     */
    public function __construct(Field $field, DB $db)
    {
        $this->field = $field;

        $this->db = $db;
    }
}
