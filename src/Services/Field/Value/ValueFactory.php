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

namespace N1ebieski\IDir\Services\Field\Value;

use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Services\Field\Value\Types\Value;
use N1ebieski\IDir\Exceptions\Field\ValueNotFoundException;
use Illuminate\Contracts\Container\BindingResolutionException;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\FileInterface;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\ArrayInterface;

class ValueFactory
{
    /**
     * Undocumented function
     *
     * @param App $app
     */
    public function __construct(protected App $app)
    {
        //
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    protected function isClassExists(string $type): bool
    {
        return class_exists($this->className($type)) || $this->app->bound($this->className($type));
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function className(string $type): string
    {
        return "N1ebieski\\IDir\\Services\\Field\\Value\\Types\\" . ucfirst($type);
    }

    /**
     *
     * @param Field $field
     * @return ArrayInterface|FileInterface
     * @throws BindingResolutionException
     * @throws ValueNotFoundException
     */
    public function makeValue(Field $field): ArrayInterface|FileInterface
    {
        if ($this->isClassExists($field->type)) {
            return $this->app->make($this->className($field->type), ['field' => $field]);
        }

        throw new \N1ebieski\IDir\Exceptions\Field\ValueNotFoundException(
            "Field value \"{$field->type}\" not found"
        );
    }
}
