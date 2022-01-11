<?php

namespace N1ebieski\IDir\View\ViewModels\Admin\Dir;

use N1ebieski\IDir\Models\Group;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;

class Create1ViewModel extends ViewModel
{
    /**
     * [$group description]
     *
     * @var Collection
     */
    public $groups;

    /**
     * Undocumented function
     *
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->groups = $group->makeRepo()->getWithRels();
    }
}
