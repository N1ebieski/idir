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
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SumRating
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
                if (!isset($value)) {
                    /** @var Collection */
                    $ratings = $this->dir->getRelation('ratings');

                    $sum_rating = $ratings->count() > 0 ? $ratings->sum('rating') / $ratings->count() : 0;

                    return number_format($sum_rating, 2, '.', '');
                }

                return $value;
            }
        );
    }
}
