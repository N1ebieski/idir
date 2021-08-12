<?php

namespace N1ebieski\IDir\Services\Field\Value;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Services\Field\Value\Types\Value;

class ValueFactory
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isClassExists(string $type): bool
    {
        return class_exists($this->className($type));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className(string $type): string
    {
        return "\\N1ebieski\\IDir\\Services\\Field\\Value\\Types\\" . ucfirst($type);
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @param GusReport $gusReport
     * @return Value
     */
    public function makeValue(Field $field): Value
    {
        if ($this->isClassExists($field->type)) {
            return $this->app->make($this->className($field->type), ['field' => $field]);
        }

        throw new \N1ebieski\IDir\Exceptions\Field\ValueNotFoundException(
            "Value {$field->type} not found",
            403
        );
    }
}
