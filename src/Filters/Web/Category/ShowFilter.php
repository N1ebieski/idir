<?php

namespace N1ebieski\IDir\Filters\Web\Category;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Filters\Traits\HasRegion;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;

class ShowFilter extends Filter
{
    use HasOrderBy;
    use HasRegion;
}
