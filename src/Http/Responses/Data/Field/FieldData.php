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
     * @var Config
     */
    protected $config;

    /**
     * Undocumented variable
     *
     * @var GusReport|null
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
     * @param Config $config
     * @param ValueFactory $valueFactory
     * @param GusReport $gusReport
     */
    public function __construct(
        Config $config,
        ValueFactory $valueFactory,
        GusReport $gusReport = null
    ) {
        $this->gusReport = $gusReport;

        $this->valueFactory = $valueFactory;

        $this->config = $config;
    }

    /**
     * Undocumented function
     *
     * @param GusReport $gusReport
     * @return self
     */
    public function setGusReport(GusReport $gusReport)
    {
        $this->gusReport = $gusReport;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->config->get('idir.field.gus') as $key => $value) {
            $id = $this->id($value);

            if ($id === null) {
                continue;
            }

            $gusValue = $this->makeValue($key);

            if (empty($gusValue)) {
                continue;
            }

            if (is_object($gusValue)) {
                $data[$id] = $gusValue;

                continue;
            }

            if (!isset($data[$id])) {
                $data[$id] = '';
            }

            $data[$id] .= $this->separator($value) . $gusValue;
        }

        return $data;
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return mixed|null
     */
    protected function makeValue(string $type)
    {
        return $this->valueFactory->makeValue($type, $this->gusReport)();
    }

    /**
     * Undocumented function
     *
     * @param mixed $value
     * @return string|null
     */
    protected function id($value): ?string
    {
        $id = $value['id'] ?? $value;

        if (is_int($id)) {
            return "field.{$id}";
        }

        return $id;
    }

    /**
     * Undocumented function
     *
     * @param mixed $value
     * @return string|null
     */
    protected function separator($value): ?string
    {
        return $value['separator'] ?? null;
    }
}
