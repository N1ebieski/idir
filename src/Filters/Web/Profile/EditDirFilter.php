<?php

namespace N1ebieski\IDir\Filters\Web\Profile;

use N1ebieski\ICore\Filters\Filter;
use N1ebieski\IDir\Filters\Traits\HasGroup;
use N1ebieski\ICore\Filters\Traits\HasExcept;
use N1ebieski\ICore\Filters\Traits\HasSearch;
use N1ebieski\ICore\Filters\Traits\HasStatus;
use N1ebieski\ICore\Filters\Traits\HasOrderBy;
use N1ebieski\ICore\Filters\Traits\HasPaginate;

class EditDirFilter extends Filter
{
    use HasExcept, HasSearch, HasStatus, HasGroup, HasOrderBy, HasPaginate;
}