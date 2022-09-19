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

class Schema1000 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/idir/web/dir/show.blade.php',
                'resources/views/vendor/idir/web/dir/partials/summary.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/\{\{ \$field->title \}\}:/',
                    'to' => '{{ $field->title }}@if (!$field->type->isSwitch()):@endif'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/idir/web/contact/dir/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/custom-checkbox/',
                    'to' => 'custom-switch'
                ]
            ]
        ],
        [
            'paths' => [
                'resources/views/vendor/idir/web/dir/show.blade.php'
            ],
            'actions' => [
                [
                    'type' => 'replace',
                    'search' => '/->toHtml\(\)->render\(\);/',
                    'to' => '->render()->render();'
                ]
            ]
        ]
    ];
}
