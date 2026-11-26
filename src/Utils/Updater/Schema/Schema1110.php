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

class Schema1110 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'config/services.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/\s*\'gus\'\s*=>\s*\[/',
                    'to' => <<<'PHP'

    'pagepeeker_v3' => [
        'api_key' => env('PAGEPEEKER_V3_API_KEY'),
        'api_secret' => env('PAGEPEEKER_V3_API_SECRET')
    ],
PHP
                ]
            ]
        ]
    ];
}
