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

namespace N1ebieski\IDir\Filters\Web\Category\Dir;

use Illuminate\Http\Request;
use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\Filters\Traits\HasRegion;
use Illuminate\Support\Collection as Collect;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;

class ShowFilter extends Filter
{
    use HasOrderBy;
    use HasRegion;

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param Collect $collect
     */
    public function __construct(Request $request, Collect $collect)
    {
        parent::__construct($request, $collect);

        if ($request->route('region_cache') instanceof Region) {
            $this->setRegion($request->route('region_cache'));
        }
    }
}
