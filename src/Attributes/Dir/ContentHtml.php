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

namespace N1ebieski\IDir\Attributes\Dir;

use N1ebieski\IDir\Models\Dir;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ContentHtml
{
    /**
     *
     * @param Dir $dir
     * @return void
     */
    public function __construct(protected Dir $dir)
    {
        //
    }

    /**
     *
     * @return Attribute
     */
    public function __invoke(): Attribute
    {
        return new Attribute(
            get: function ($value): string {
                if ($this->dir->getRelation('group')->hasEditorPrivilege()) {
                    return $value;
                }

                return strip_tags($value);
            },
            set: function ($value): string {
                return Purifier::clean($value, 'dir');
            }
        );
    }
}
