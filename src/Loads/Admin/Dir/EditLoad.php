<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

class EditLoad
{
    /**
     * [__construct description]
     * @param Request $request [description]
     */
    public function __construct(Request $request)
    {
        $request->route('dir')->load([
            'group',
            'group.privileges',
            'group.fields' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'regions',
            'categories' => function ($query) {
                $query->withAncestorsExceptSelf();
            },
            'tags'
        ]);
    }
}
