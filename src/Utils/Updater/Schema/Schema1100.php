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

class Schema1100 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/idir/web/dir/create/2.blade.php',
            ],
            'actions' => [
                [
                    'type' => [
                        'move',
                        'afterFirst'
                    ],
                    'search' => '/\@if\s*\(\s*\!\$group->url->isInactive\(\)\s*\)[\s\S]*?<div[\s\S]*?\/div>[\s\S]*?\@endif/',
                    'to' => '/<div\s*class="form-group">[\s\S]*?<label\s*for="title"[\s\S]*?\[\'name\'\s*=>\s*\'title\'\]\)[\s\S]*?<\/div>/'
                ]
            ]
        ]
    ];
}
