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

namespace N1ebieski\IDir\Http\Responses\Data\Gus\Field;

use GusApi\SearchReport as GusReport;
use Illuminate\Contracts\Config\Repository as Config;
use N1ebieski\IDir\Http\Responses\Data\Gus\DataInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Http\Responses\Data\Gus\Field\Value\ValueFactory;

class FieldData implements DataInterface
{
    /**
     * Undocumented function
     *
     * @param Config $config
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        protected Config $config,
        protected ValueFactory $valueFactory
    ) {
        //
    }

    /**
     *
     * @param GusReport $gusReport
     * @return array
     * @throws BindingResolutionException
     */
    public function toArray(GusReport $gusReport): array
    {
        $data = [];

        foreach ($this->config->get('idir.field.gus') as $key => $value) {
            $id = $this->id($value);

            if ($id === null) {
                continue;
            }

            $gusValue = $this->valueFactory->makeValue($key, $gusReport)->handle();

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
