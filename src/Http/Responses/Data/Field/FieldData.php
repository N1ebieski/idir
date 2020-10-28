<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field;

use GusApi\SearchReport as GusReport;
use N1ebieski\IDir\Http\Responses\Data\DataInterface;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\Types\Value;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\ValueFactory;

class FieldData implements DataInterface
{
    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented variable
     *
     * @var array|null
     */
    protected $fields;

    /**
     * Undocumented variable
     *
     * @var GusReport
     */
    protected $gusReport;

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @param Config $config
     */
    public function __construct(GusReport $gusReport, Config $config, App $app)
    {
        $this->gusReport = $gusReport;

        $this->app = $app;

        $this->fields = $config->get('idir.field.gus');
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray() : array
    {
        $data = [];

        foreach ($this->fields as $key => $value) {
            $id = $this->id($value);

            if ($id === null) {
                continue;
            }

            $gusValue = $this->makeValue($key)();

            if (empty($gusValue)) {
                continue;
            }

            if (!isset($data["field.{$id}"])) {
                $data["field.{$id}"] = '';
            }

            $data["field.{$id}"] .= $this->separator($value) . $gusValue;
        }

        return $data;
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return Value
     */
    protected function makeValue(string $type) : Value
    {
        return $this->app->make(ValueFactory::class, [
            'type' => $type,
            'gusReport' => $this->gusReport
        ])
        ->makeValue();
    }

    /**
     * Undocumented function
     *
     * @param mixed $value
     * @return integer|null
     */
    protected function id($value) : ?int
    {
        if (is_int($value)) {
            return $value;
        }

        return $value['id'] ?? null;
    }

    /**
     * Undocumented function
     *
     * @param mixed $value
     * @return string|null
     */
    protected function separator($value) : ?string
    {
        return $value['separator'] ?? null;
    }
}
