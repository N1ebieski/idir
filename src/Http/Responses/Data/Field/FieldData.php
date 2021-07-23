<?php

namespace N1ebieski\IDir\Http\Responses\Data\Field;

use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Responses\Data\DataInterface;
use N1ebieski\IDir\Http\Responses\Data\Field\Value\ValueFactory;

class FieldData implements DataInterface
{
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
     * Undocumented variable
     *
     * @var ValueFactory
     */
    protected $valueFactory;

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @param Config $config
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        GusReport $gusReport,
        Config $config,
        ValueFactory $valueFactory
    ) {
        $this->gusReport = $gusReport;

        $this->valueFactory = $valueFactory;

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

            $gusValue = $this->makeValue($key);

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
     * @return string|null
     */
    protected function makeValue(string $type) : ?string
    {
        return $this->valueFactory->makeValue($type, $this->gusReport)();
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
