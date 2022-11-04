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

namespace N1ebieski\IDir\Utils\Updater\Schema;

use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Schema1020 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/idir/web/partials/nav.blade.php',
            ],
            'actions' => [
                [
                    'type' => 'afterFirst',
                    'search' => '/@if\s*\(app\(\'router\'\)->has\(\'web\.dir\.create_1\'\)\)/',
                    'to' => <<<EOD
                <li class="nav-item mx-md-1 dropdown">
                    <x-icore::theme-component />
                </li>
EOD
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/idir/web/partials/footer.blade.php',
            ],
            'actions' => [
                [
                    'type' => 'removeFirst',
                    'search' => '/<div[^>]*?id="theme-toggle"[\s\S]*?>[\s\S]*?<\/div>/'
                ]
            ]
        ]
    ];
}
