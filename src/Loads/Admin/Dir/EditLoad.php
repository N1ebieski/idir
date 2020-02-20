<?php

namespace N1ebieski\IDir\Loads\Admin\Dir;

use Illuminate\Http\Request;

/**
 * [EditLoad description]
 */
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
            'group.fields',
            'regions',
            'fields',
            'categories' => function ($query) {
                $query->withAncestorsExceptSelf();
            },
            'tags'
        ]);
    }
}
