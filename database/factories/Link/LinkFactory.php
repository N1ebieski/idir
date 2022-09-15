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

namespace N1ebieski\IDir\Database\Factories\Link;

use N1ebieski\IDir\Models\Link;
use N1ebieski\ICore\Database\Factories\Link\LinkFactory as BaseLinkFactory;

class LinkFactory extends BaseLinkFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Link>
     */
    protected $model = Link::class;

    /**
     * Undocumented function
     *
     * @return static
     */
    public function backlink()
    {
        return $this->state(function () {
            return [
                'type' => 'backlink',
            ];
        });
    }
}
