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

namespace N1ebieski\IDir\Utils\Thumbnail\Interfaces;

interface ThumbnailInterface
{
    /**
     * Undocumented function
     *
     * @param string $url
     * @return self
     */
    public function make(string $url): self;

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLastModified(): string;

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isReload(): bool;

    /**
     * Undocumented function
     *
     * @return bool|string|null
     */
    public function generate(): bool|string|null;

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function reload(): bool;
}
