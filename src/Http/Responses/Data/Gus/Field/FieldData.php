<?php

namespace N1ebieski\IDir\Http\Responses\Data\Gus\Field;

use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Responses\Data\Gus\DataInterface;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\ValueFactory;

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
     * @var ValueFactory
     */
    protected $valueFactory;

    /**
     * Undocumented function
     *
     * @param Config $config
     * @param ValueFactory $valueFactory
     */
    public function __construct(Config $config, ValueFactory $valueFactory)
    {
        $this->valueFactory = $valueFactory;

        $this->config = $config;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function toArray(GusReport $gusReport): array
    {
        $data = [];

        foreach ($this->config->get('idir.field.gus') as $key => $value) {
            $id = $this->id($value);

            if ($id === null) {
                continue;
            }

            $gusValue = $this->valueFactory->makeValue($key, $gusReport)();

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
