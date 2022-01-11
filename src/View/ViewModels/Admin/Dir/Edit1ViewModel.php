<?php

namespace N1ebieski\IDir\View\ViewModels\Admin\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use Spatie\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;

class Edit1ViewModel extends ViewModel
{
    /**
     * Undocumented variable
     *
     * @var Dir
     */
    public $dir;

    /**
     * [$group description]
     *
     * @var Collection
     */
    public $groups;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Group $group
     */
    public function __construct(Dir $dir, Group $group)
    {
        $this->dir = $dir;

        $this->groups = $group->makeRepo()->getWithRels();
    }
}
