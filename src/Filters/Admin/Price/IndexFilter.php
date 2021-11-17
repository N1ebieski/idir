<?php

namespace N1ebieski\IDir\Filters\Admin\Price;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\ICore\Filters\Traits\HasType;
use N1ebieski\IDir\Filters\Traits\HasGroup;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class IndexFilter extends Filter
{
    use HasExcept;
    use HasSearch;
    use HasGroup;
    use HasType;
    use HasOrderBy;
    use HasPaginate;
}
